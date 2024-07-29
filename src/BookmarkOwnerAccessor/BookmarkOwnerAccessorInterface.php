<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle\BookmarkOwnerAccessor;

use Presta\SonataBookmarksBundle\Entity\BookmarkOwnerInterface;

interface BookmarkOwnerAccessorInterface
{
    public function get(): BookmarkOwnerInterface;
}
