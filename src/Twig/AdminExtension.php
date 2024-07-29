<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle\Twig;

use Presta\SonataBookmarksBundle\Entity\Bookmark;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AdminExtension extends AbstractExtension
{
    public function __construct(private Pool $sonata)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'get_presta_sonata_bookmarks_admin',
                function (): AdminInterface {
                    return $this->sonata->getAdminByClass(Bookmark::class);
                }
            ),
        ];
    }
}
