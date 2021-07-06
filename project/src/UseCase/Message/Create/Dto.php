<?php

namespace App\UseCase\Message\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Dto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public string $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Email()
     */
    public string $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public string $phone;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public string $text;
}