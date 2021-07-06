<?php

namespace App\Service;

use App\Repository\PostRepository;

class PopularPostsService
{
    private PostRepository $posts;

    public function __construct(PostRepository $posts)
    {
        $this->posts = $posts;
    }

    public function getPopular(): array
    {
        return $this->posts->findPopular();
    }
}