<?php declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\UserCreation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @implements ProcessorInterface<UserCreation, User>
 */
class UserCreationProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): User {
        $user = new User();
        $user->setEmail($data->email);
        $user->setRole('ROLE_USER');

        // Hash du mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $data->password
        );
        $user->setPassword($hashedPassword);

        // Sauvegarde en base
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
