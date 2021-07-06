<?php

namespace App\Event;

use App\Entity\Category;
use Symfony\Contracts\EventDispatcher\Event;

class CategoryCreatedEvent extends Event
{
    public const NAME = 'category.created';

    private Category $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}