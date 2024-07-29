<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle\Admin;

use Presta\SonataBookmarksBundle\BookmarkOwnerAccessor\BookmarkOwnerAccessorInterface;
use Presta\SonataBookmarksBundle\Entity\Bookmark;
use Presta\SonataBookmarksBundle\Exception\UnexpectedTypeException;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends AbstractAdmin<Bookmark>
 */
final class BookmarkAdmin extends AbstractAdmin
{
    public function __construct(
        private readonly BookmarkOwnerAccessorInterface $bookmarkHolderAccessor,
    ) {
        parent::__construct();
    }

    protected function generateBaseRouteName(bool $isChildAdmin = false): string
    {
        return 'presta_sonatabookmark_bookmark';
    }

    protected function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return '/presta/sonata-bookmark/bookmark';
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query = parent::configureQuery($query);
        if (!$query instanceof ProxyQuery) {
            throw new UnexpectedTypeException($query, ProxyQuery::class);
        }

        $rootAlias = current($query->getRootAliases());

        $query
            ->andWhere("{$rootAlias}.owner = :owner")
            ->setParameter('owner', $this->bookmarkHolderAccessor->get())
        ;

        return $query;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->clearExcept(['list', 'delete', 'batch']);

        $collection->add(
            name: 'new',
            pattern: 'new',
            methods: [Request::METHOD_POST],
        );
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->add('name', null, ['label' => 'bookmark.field.name']);
        $list->add('url', null, ['label' => 'bookmark.field.url']);

        $list->add(
            '_action',
            'actions',
            [
                'label' => 'bookmark.field._action',
                'actions' => [
                    'delete' => [],
                ],
            ],
        );
    }
}
