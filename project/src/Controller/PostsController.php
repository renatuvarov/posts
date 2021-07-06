<?php

namespace App\Controller;

use App\Entity\Post;
use App\Event\PostViewedEvent;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Service\PopularPostsService;
use App\UseCase\Post\Create\Dto;
use App\UseCase\Post\Create\Interactor;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/', name: 'posts.')]
class PostsController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(Request $request, PostRepository $posts): Response
    {
        return $this->render('posts/index.html.twig', [
            'pagination' => $posts->paginate($request->get('page', 1)),
        ]);
    }

    #[Route('/posts/create', name: 'create')]
    public function create(
        Request $request,
        Interactor $interactor,
        CategoryRepository $categories,
        LoggerInterface $logger,
    ): Response
    {
        if ($categories->countCategories() < 1) {
            return $this->redirectToRoute('categories.create');
        }

        $dto = new Dto();

        $form = $this->createForm(PostType::class, $dto);
        $form->handleRequest($request);

        $img = $form->get('img')->getData();
        if ($img) {
            $dto->img = $img;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $interactor($dto);
                return $this->redirectToRoute('posts.index');
            } catch (Exception $e) {
                $logger->error($e->getMessage());
            }
        }

        return $this->render('posts/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/posts/{post}', name: 'view')]
    public function view(Post $post, EventDispatcherInterface $dispatcher, PostRepository $posts): Response
    {
        $dispatcher->dispatch(new PostViewedEvent($post), PostViewedEvent::NAME);

        return $this->render('posts/view.html.twig', [
            'post' => $post,
            'related' => $posts->relatedPosts($post),
        ]);
    }
}
