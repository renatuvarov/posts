<?php

namespace App\UseCase\Category\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Dto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public string $title;
}