<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle\Tests\Admin;

use Presta\SonataBookmarksBundle\Entity\Bookmark;
use Presta\SonataBookmarksBundle\Tests\App\User;
use Symfony\Component\HttpFoundation\Response;

final class BookmarkAdminNewTest extends AdminTestCase
{
    public function testNew(): void
    {
        // Given
        self::$doctrine->persist($admin = new User('admin'));
        self::$doctrine->flush();
        self::$client->loginUser($admin);
        self::assertSame(0, self::$doctrine->getRepository(Bookmark::class)->count([]));

        // When
        self::$client->request('GET', '/presta/sonata-bookmark/bookmark/list');
        self::$client->request('POST', '/presta/sonata-bookmark/bookmark/new', [
            'name' => 'My precious bookmark',
            'route' => 'presta_sonatabookmark_bookmark_list',
        ]);

        // Then
        self::assertResponseIsSuccessful();
        self::assertJson(self::$client->getResponse()->getContent());
        self::assertSame(1, self::$doctrine->getRepository(Bookmark::class)->count([]));
    }

    public function testNewInvalid(): void
    {
        // Given
        self::$doctrine->persist($admin = new User('admin'));
        self::$doctrine->flush();
        self::$client->loginUser($admin);
        self::assertSame(0, self::$doctrine->getRepository(Bookmark::class)->count([]));

        // When
        self::$client->request('GET', '/presta/sonata-bookmark/bookmark/list');
        self::$client->request('POST', '/presta/sonata-bookmark/bookmark/new', [
            'name' => null,
        ]);

        // Then
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertJson(self::$client->getResponse()->getContent());
        self::assertSame(0, self::$doctrine->getRepository(Bookmark::class)->count([]));
    }

    public function testNewNotAuthenticated(): void
    {
        // Given
        self::assertSame(0, self::$doctrine->getRepository(Bookmark::class)->count([]));

        // When
        self::$client->request('GET', '/presta/sonata-bookmark/bookmark/list');
        self::$client->request('POST', '/presta/sonata-bookmark/bookmark/new');

        // Then
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertSame(0, self::$doctrine->getRepository(Bookmark::class)->count([]));
    }
}
