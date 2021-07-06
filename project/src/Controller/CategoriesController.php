<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\PostRepository;
use App\UseCase\Category\Create\Dto;
use App\UseCase\Category\Create\Interactor;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories', name: 'categories.')]
class CategoriesController extends AbstractController
{
    #[Route('/create', name: 'create')]
    public function create(
        Request $request,
        Interactor $interactor,
        LoggerInterface $logger,
    ): Response
    {
        $dto = new Dto();

        $form = $this->createForm(CategoryType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $interactor($dto);
                return $this->redirectToRoute('posts.index');
            } catch (Exception $e) {
                $logger->error($e->getMessage());
            }
        }

        return $this->render('categories/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{category}', name: 'view')]
    public function view(Request $request, Category $category, PostRepository $posts): Response
    {
        return $this->render('categories/view.html.twig', [
            'pagination' => $posts->paginate($request->get('page', 1), $category->getId()),
            'category' => $category,
        ]);
    }
}
