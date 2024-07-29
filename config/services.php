<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Presta\SonataBookmarksBundle\Admin\BookmarkAdmin;
use Presta\SonataBookmarksBundle\BookmarkOwnerAccessor\AuthenticatedUserBookmarkOwnerAccessor;
use Presta\SonataBookmarksBundle\BookmarkOwnerAccessor\BookmarkOwnerAccessorInterface;
use Presta\SonataBookmarksBundle\Controller\BookmarkController;
use Presta\SonataBookmarksBundle\Entity\Bookmark;
use Presta\SonataBookmarksBundle\Repository\BookmarkRepository;
use Presta\SonataBookmarksBundle\Twig\AdminExtension;
use Presta\SonataBookmarksBundle\Twig\BookmarkExtension;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set(BookmarkAdmin::class)
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'code' => 'presta_sonata_bookmark.bookmark',
                'label' => 'bookmark.name',
                'model_class' => Bookmark::class,
                'controller' => BookmarkController::class,
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
            ])
            ->args([
                service(BookmarkOwnerAccessorInterface::class),
            ])
            ->call('setTranslationDomain', ['PrestaSonataBookmarksBundle'])

        ->set(BookmarkController::class)
            ->autowire()
            ->tag('controller.service_arguments')
            ->tag('container.service_subscriber')
            ->args([
                service('doctrine.orm.entity_manager'),
                service('router.default'),
                service(BookmarkRepository::class),
                service(BookmarkOwnerAccessorInterface::class),
                service('validator'),
            ])

        ->alias(BookmarkOwnerAccessorInterface::class, AuthenticatedUserBookmarkOwnerAccessor::class)

        ->set(AuthenticatedUserBookmarkOwnerAccessor::class)
            ->args([
                service(TokenStorageInterface::class),
            ])

        ->set(BookmarkRepository::class)
            ->tag('doctrine.repository_service')
            ->args([
                service('doctrine'),
            ])

        ->set(BookmarkExtension::class)
            ->tag('twig.extension')
            ->args([
                service(BookmarkRepository::class),
                service(BookmarkOwnerAccessorInterface::class),
            ])

        ->set(AdminExtension::class)
            ->tag('twig.extension')
            ->args([
                service(Pool::class),
            ])
    ;
};
