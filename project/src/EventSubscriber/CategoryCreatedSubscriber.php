<?php

namespace App\EventSubscriber;

use App\Event\CategoryCreatedEvent;
use App\Service\CategoriesCacheService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CategoryCreatedSubscriber implements EventSubscriberInterface
{
    private CategoriesCacheService $cacheService;

    public function __construct(CategoriesCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function onCategoryCreated(CategoryCreatedEvent $event)
    {
        $this->cacheService->clearCache();
        $this->cacheService->findOrCreateCache();
    }

    public static function getSubscribedEvents()
    {
        return [
            CategoryCreatedEvent::NAME => 'onCategoryCreated',
        ];
    }
}
