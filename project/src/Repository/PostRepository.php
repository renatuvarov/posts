<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    private const POPULAR_LIMIT = 3;
    private const RELATED_LIMIT = 3;
    private const PAGE_LIMIT = 1;

    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Post::class);
        $this->paginator = $paginator;
    }

    public function paginate(int $page, int $categoryId = null)
    {
        $qb = $this->createQueryBuilder('p');

        $query = $qb->innerJoin('p.categories', 'c');

        if ($categoryId) {
            $query->andWhere($qb->expr()->eq('c.id', $categoryId));
        }

        return $this->paginator->paginate(
            $query,
            $page,
            self::PAGE_LIMIT
        );
    }

    /**
    * @return Post[]
    */
    public function findPopular()
    {
        return $this->createQueryBuilder('p')
            ->where('p.views > 0')
            ->innerJoin('p.categories', 'c')
            ->orderBy('p.views', 'DESC')
            ->addOrderBy('p.created_at', 'DESC')
            ->setMaxResults(self::POPULAR_LIMIT)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Post[]
     */
    public function findByCategoryId(int $categoryId)
    {
        $qb = $this->createQueryBuilder('p');
        return $qb->join('p.categories', 'c')
            ->where($qb->expr()->eq('c.id', $categoryId))
            ->orderBy('c.created_at', 'DESC')
            ->setMaxResults(self::PAGE_LIMIT)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findById(int $id): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Post[]
     */
    public function relatedPosts(Post $post): array
    {
        $ids = $post->getCategoriesIds();

        return $this->createQueryBuilder('p')
            ->innerJoin('p.categories', 'c')
            ->andWhere('p.id <> :id')
            ->andWhere('c.id in (:ids)')
            ->setParameter('ids', $ids)
            ->setParameter('id', $post->getId())
            ->setMaxResults(self::RELATED_LIMIT)
            ->orderBy('p.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
