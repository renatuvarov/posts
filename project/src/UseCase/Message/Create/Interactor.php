<?php

namespace App\UseCase\Message\Create;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;

class Interactor
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(Dto $dto)
    {
        $message = new Message();
        $message->setName($dto->name);
        $message->setEmail($dto->email);
        $message->setPhone($dto->phone);
        $message->setText($dto->text);

        $this->em->persist($message);
        $this->em->flush();
    }
}