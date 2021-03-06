<?php

declare(strict_types=1);

/*
 * This file is part of the Compotes package.
 *
 * (c) Alex "Pierstoval" Rock <pierstoval@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 * @ORM\Table(name="tags")
 * @ORM\ChangeTrackingPolicy(value="DEFERRED_EXPLICIT")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private string $name = '';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tag", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private ?Tag $parent = null;

    public function __toString(): string
    {
        $names = [];

        $parent = $this->parent;

        while ($parent) {
            \array_unshift($names, $parent->getName());

            $parent = $parent->getParent();
        }

        $names[] = $this->name;

        return \implode(' > ', $names);
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function getName(): string
    {
        return (string) $this->name;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setName(?string $name): void
    {
        if ($name) {
            $name = (new AsciiSlugger())->slug($name);
        }

        $this->name = (string) $name;
    }

    public function setParent(?self $parent): void
    {
        $this->parent = $parent;
    }
}
