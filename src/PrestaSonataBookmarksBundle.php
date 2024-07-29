<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

final class PrestaSonataBookmarksBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
