<?php

namespace App\UseCase\Category\Create;

use App\Entity\Category;
use App\Event\CategoryCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

class Interactor
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;
    private SluggerInterface $slugger;

    public function __construct(
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        SluggerInterface $slugger,
    )
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->slugger = $slugger;
    }

    public function __invoke(Dto $dto): void
    {
        $category = new Category();
        $category->setTitle($dto->title);
        $category->setSlug($this->slugger->slug($dto->title)->toString());

        $this->em->persist($category);
        $this->em->flush();

        $this->dispatcher->dispatch(new CategoryCreatedEvent($category), CategoryCreatedEvent::NAME);
    }
}