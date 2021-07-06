<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CategoriesCacheService
{
    private const TAG = 'categories_list';

    private CategoryRepository $categories;
    private CacheInterface $categoriesCache;

    public function __construct(CategoryRepository $categories, CacheInterface $categoriesCache)
    {
        $this->categories = $categories;
        $this->categoriesCache = $categoriesCache;
    }

    /**
     * @return CategoryView[]
     * @throws InvalidArgumentException
     */
    public function getCategories(): array
    {
        $list = $this->findOrCreateCache();

        $views = [];
        foreach ($list as $item) {
            $views[] = new CategoryView($item['id'], $item['title']);
        }

        return $views;
    }

    public function findOrCreateCache(): array
    {
        return $this->categoriesCache->get(self::TAG, function (ItemInterface $item) {
            return $this->categories->findAllArray();
        });
    }

    public function clearCache(): void
    {
        $this->categoriesCache->delete(self::TAG);
    }
}