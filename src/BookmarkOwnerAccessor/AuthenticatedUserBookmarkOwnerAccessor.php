<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle\BookmarkOwnerAccessor;

use Presta\SonataBookmarksBundle\Entity\BookmarkOwnerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AuthenticatedUserBookmarkOwnerAccessor implements BookmarkOwnerAccessorInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly KernelInterface $kernel,
    ) {
    }

    public function get(): BookmarkOwnerInterface
    {
        $user = $this->tokenStorage->getToken()?->getUser();
        if (null === $user) {
            // In CLI, there is no authenticated user.
            if (PHP_SAPI === 'cli' && 'test' !== $this->kernel->getEnvironment()) {
                // Return a dummy object that satisfies the interface to prevent commands from crashing.
                return new class() implements BookmarkOwnerInterface {
                };
            }

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
