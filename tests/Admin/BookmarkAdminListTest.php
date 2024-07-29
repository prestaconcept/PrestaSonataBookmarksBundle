<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle\Tests\Admin;

use Presta\SonataBookmarksBundle\Entity\Bookmark;
use Presta\SonataBookmarksBundle\Tests\App\User;

final class BookmarkAdminListTest extends AdminTestCase
{
    public function testList(): void
    {
        // Given
        self::$doctrine->persist($user = new User('admin'));
        self::$doctrine->persist($filter = new Bookmark());
        $filter->setName('My filters');
        $filter->setUrl('https://test-list');
        $filter->setOwner($user);
        self::$doctrine->flush();
        self::$client->loginUser($user);

        // When
        $page = self::$client->request('GET', '/presta/sonata-bookmark/bookmark/list');

        // Then
        self::assertResponseIsSuccessful();
        self::assertPageContainsCountElement(1, $page);
    }
}
