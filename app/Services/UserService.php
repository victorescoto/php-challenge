<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\User;
use Doctrine\ORM\EntityManager;

final class UserService
{
    public function __construct(private EntityManager $em) {}

    public function create(string $email, string $password): User
    {
        $newUser = new User($email, $password);

        $this->em->persist($newUser);
        $this->em->flush();

        return $newUser;
    }

    public function get(int $id): ?User
    {
        return $this->em->find(User::class, $id);
    }

    public function getByEmail(string $email): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
    }
}
