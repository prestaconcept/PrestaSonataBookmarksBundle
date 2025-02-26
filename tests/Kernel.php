<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Presta\SonataBookmarksBundle\Entity\BookmarkOwnerInterface;
use Presta\SonataBookmarksBundle\PrestaSonataBookmarksBundle;
use Presta\SonataBookmarksBundle\Tests\App\User;
use Presta\SonataBookmarksBundle\Tests\App\UserAdmin;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\BlockBundle\SonataBlockBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Sonata\Twig\Bridge\Symfony\SonataTwigBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new TwigBundle();
        yield new SecurityBundle();
        yield new DoctrineBundle();
        yield new KnpMenuBundle();
        yield new SonataAdminBundle();
        yield new SonataTwigBundle();
        yield new SonataBlockBundle();
        yield new SonataDoctrineORMAdminBundle();
        yield new PrestaSonataBookmarksBundle();
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->services()->set('logger', Logger::class)
            ->args(['$output' => '%kernel.logs_dir%/test.log'])
            ->public(true);

        $container->services()->set(UserAdmin::class)
            ->tag('sonata.admin', ['manager_type' => 'orm', 'model_class' => User::class]);

        $container->extension('framework', [
            'test' => true,
            'secret' => '$ecretf0rt3st',
            'session' => [
                'handler_id' => null,
                'cookie_secure' => 'auto',
                'cookie_samesite' => 'lax',
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
        ]);
        $container->extension('security', [
            'password_hashers' => [
                User::class => 'auto',
            ],
            'providers' => [
                'user' => ['entity' => ['class' => User::class, 'property' => 'username']],
            ],
            'firewalls' => [
                'main' => [
                    'pattern' => '^/',
                    'provider' => 'user',
                    'http_basic' => ['realm' => 'Secured Area'],
                ],
            ],
        ]);
        $container->extension('doctrine', [
            'dbal' => [
                'url' => 'sqlite:///%kernel.project_dir%/var/database.sqlite',
                'logging' => false,
            ],
            'orm' => [
                'auto_generate_proxy_classes' => true,
                'naming_strategy' => 'doctrine.orm.naming_strategy.underscore',
                'mappings' => [
                    'PrestaSonataBookmarksBundle' => [
                        'is_bundle' => true,
                        'type' => 'attribute',
                        'dir' => 'src/Entity',
                        'prefix' => 'Presta\\SonataBookmarksBundle\\Entity',
                        'alias' => 'PrestaSonataBookmarksBundle',
                    ],
                    'Tests' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => 'tests/App',
                        'prefix' => 'Presta\\SonataBookmarksBundle\\Tests\\App',
                        'alias' => 'Tests',
                    ],
                ],
                'resolve_target_entities' => [
                    BookmarkOwnerInterface::class => User::class,
                ],
            ],
        ]);
        $container->extension('twig', [
            'default_path' => __DIR__ . '/App/templates',
        ]);
        $container->extension('sonata_admin', [
            'templates' => [
                'layout' => 'standard_layout.html.twig',
            ],
        ]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('@SonataAdminBundle/Resources/config/routing/sonata_admin.xml');
        $routes->import('.', 'sonata_admin');
    }
}
