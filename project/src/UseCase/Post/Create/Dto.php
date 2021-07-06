<?php

namespace App\UseCase\Post\Create;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class Dto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public string $title;

    /**
     * @Assert\NotBlank()
     */
    public string $text;

    /**
     * @Assert\Type("array")
     * @Assert\Count(min=1)
     */
    public array $categories;

    /**
     * @Assert\Image()
     */
    public ?UploadedFile $img;
}