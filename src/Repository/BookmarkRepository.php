<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Presta\SonataBookmarksBundle\Entity\Bookmark;
use Presta\SonataBookmarksBundle\Entity\BookmarkOwnerInterface;

/**
 * @extends ServiceEntityRepository<Bookmark>
 */
final class BookmarkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bookmark::class);
    }

    /**
     * @return array<Bookmark>
     */
    public function findAccessibleForOwner(BookmarkOwnerInterface $owner): array
    {
        return $this->findBy(['owner' => $owner]);
    }
}
