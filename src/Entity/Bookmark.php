<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Presta\SonataBookmarksBundle\Repository\BookmarkRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookmarkRepository::class)]
#[ORM\Table(name: 'presta_sonata_bookmark')]
class Bookmark
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    private ?string $name = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $url = null;

    #[ORM\ManyToOne(targetEntity: BookmarkOwnerInterface::class)]
    private BookmarkOwnerInterface $owner;

    public function __toString(): string
    {
        return (string)$this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getOwner(): BookmarkOwnerInterface
    {
        return $this->owner;
    }

    public function setOwner(BookmarkOwnerInterface $owner): void
    {
        $this->owner = $owner;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }
}
