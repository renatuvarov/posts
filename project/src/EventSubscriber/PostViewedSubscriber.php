<?php

namespace App\EventSubscriber;

use App\Event\PostViewedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PostViewedSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onPostViewed(PostViewedEvent $event)
    {
        $post = $event->getPost();
        $post->newView();

        $this->em->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            PostViewedEvent::NAME => 'onPostViewed',
        ];
    }
}
