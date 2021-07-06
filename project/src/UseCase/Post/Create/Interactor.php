<?php

namespace App\UseCase\Post\Create;

use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use HTMLPurifier;
use Symfony\Component\String\Slugger\SluggerInterface;

class Interactor
{
    private EntityManagerInterface $em;
    private SluggerInterface $slugger;
    private FileUploadService $fileUploader;
    private CategoryRepository $categories;
    private HTMLPurifier $purifier;

    public function __construct(
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        FileUploadService $fileUploader,
        CategoryRepository $categories,
        HTMLPurifier $purifier,
    )
    {
        $this->em = $em;
        $this->slugger = $slugger;
        $this->fileUploader = $fileUploader;
        $this->categories = $categories;
        $this->purifier = $purifier;
    }

    public function __invoke(Dto $dto)
    {
        $post = new Post();

        $post->setTitle($dto->title);
        $post->setSlug($this->slugger->slug($dto->title)->toString());

        $txt = $this->purifier->purify($dto->text);
        $post->setShortText(mb_strimwidth($txt, 0, 30, '...'));
        $post->setText($this->textToHtml($txt));

        $post->setImg($this->fileUploader->upload($dto->img));

        $this->bindCategories($post, $dto->categories);

        $this->em->persist($post);
        $this->em->flush();
    }

    private function textToHtml(string $text): string
    {
        $html = '';

        foreach (explode(PHP_EOL, $text) as $str) {
            $html .= '<p>' . $str . '</p>';
        }

        return $html;
    }

    private function bindCategories(Post $post, array $categories): void
    {
        $categoriesList = $this->categories->findBy(['id' => array_values($categories)]);

        if (empty($categoriesList)) {
            throw new DomainException('Undefined Category');
        }

        foreach ($categoriesList as $category) {
            $post->addCategory($category);
        }
    }
}