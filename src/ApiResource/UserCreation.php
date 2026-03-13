<?php declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\Post;
use App\State\UserCreationProcessor;

#[Post(processor: UserCreationProcessor::class)]
class UserCreation
{
    public string $email;
    public string $password;
}
