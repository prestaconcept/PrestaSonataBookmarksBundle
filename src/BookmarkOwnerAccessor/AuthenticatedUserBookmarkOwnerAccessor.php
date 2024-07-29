<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle\BookmarkOwnerAccessor;

use Presta\SonataBookmarksBundle\Entity\BookmarkOwnerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AuthenticatedUserBookmarkOwnerAccessor implements BookmarkOwnerAccessorInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function get(): BookmarkOwnerInterface
    {
        $user = $this->tokenStorage->getToken()?->getUser();
        if ($user === null) {
            throw new CannotAccessBookmarkOwnerException('Missing authenticated user.');
        }

        if (!$user instanceof BookmarkOwnerInterface) {
            throw new CannotAccessBookmarkOwnerException(
                \sprintf(
                    'Authenticated user %s does not implements %s',
                    $user::class,
                    BookmarkOwnerInterface::class
                ),
            );
        }

        return $user;
    }
}
