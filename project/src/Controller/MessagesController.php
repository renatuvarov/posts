<?php

namespace App\Controller;

use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\UseCase\Message\Create\Dto;
use App\UseCase\Message\Create\Interactor;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/messages', name: 'messages.')]
class MessagesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(MessageRepository $messages): Response
    {
        return $this->render('messages/index.html.twig', [
            'messages' => $messages->findAll(),
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, Interactor $interactor, LoggerInterface $logger): Response
    {
        $dto = new Dto();

        $form = $this->createForm(MessageType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $interactor($dto);
                return $this->redirectToRoute('messages.index');
            } catch (Exception $e) {
                $logger->error($e->getMessage());
            }
        }

        return $this->render('messages/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
