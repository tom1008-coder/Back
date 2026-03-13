<?php declare(strict_types=1);

namespace App\Api\Extensions;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use App\Enum\RoleEnum;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class UsersListingExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $this->filter($queryBuilder, $resourceClass);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $this->filter($queryBuilder, $resourceClass);
    }

    private function filter(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        // L'extension s'applique uniquement à l'entité User
        if (User::class !== $resourceClass) {
            return;
        }

        // Si Admin ou Agent → pas de restriction
        if (
            $this->security->isGranted(RoleEnum::ROLE_ADMIN)
            || $this->security->isGranted(RoleEnum::ROLE_AGENT)
        ) {
            return;
        }

        // Sinon (ROLE_USER classique) → limiter aux ADMIN + AGENT
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like("{$rootAlias}.roles", ':role_admin'),
                    $queryBuilder->expr()->like("{$rootAlias}.roles", ':role_agent')
                )
            )
            ->setParameter('role_admin', '%' . RoleEnum::ROLE_ADMIN . '%')
            ->setParameter('role_agent', '%' . RoleEnum::ROLE_AGENT . '%');
    }
}
