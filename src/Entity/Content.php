<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContentRepository")
 */
class Content
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $contentArt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Article", inversedBy="contents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $article;

    /**
     * @ORM\Column(type="integer")
     */
    private $orderArt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContentArt(): ?string
    {
        return $this->contentArt;
    }

    public function setContentArt(string $contentArt): self
    {
        $this->contentArt = $contentArt;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getOrderArt(): ?int
    {
        return $this->orderArt;
    }

    public function setOrderArt(int $orderArt): self
    {
        $this->orderArt = $orderArt;

        return $this;
    }
}
