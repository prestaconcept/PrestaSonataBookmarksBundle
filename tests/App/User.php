<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle\Tests\App;

use Doctrine\ORM\Mapping as ORM;
use Presta\SonataBookmarksBundle\Entity\BookmarkOwnerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
class User implements UserInterface, BookmarkOwnerInterface
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}
