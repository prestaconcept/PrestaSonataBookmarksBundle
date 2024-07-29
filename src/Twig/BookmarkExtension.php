<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle\Twig;

use Presta\SonataBookmarksBundle\BookmarkOwnerAccessor\BookmarkOwnerAccessorInterface;
use Presta\SonataBookmarksBundle\BookmarkOwnerAccessor\CannotAccessBookmarkOwnerException;
use Presta\SonataBookmarksBundle\Repository\BookmarkRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class BookmarkExtension extends AbstractExtension
{
    public function __construct(
        private readonly BookmarkRepository $repository,
        private readonly BookmarkOwnerAccessorInterface $bookmarkHolderAccessor,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'get_presta_sonata_bookmarks',
                function () {
                    try {
                        $owner = $this->bookmarkHolderAccessor->get();
                    } catch (CannotAccessBookmarkOwnerException) {
                        return [];
                    }

                    return $this->repository->findAccessibleForOwner($owner);
                },
            ),
        ];
    }
}
