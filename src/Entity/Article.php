<?php

namespace App\Entity;

use App\Core\Entity;
use App\Core\TimestampableEntity;
use App\Services\Slugifier;

class Article extends Entity
{
    use TimestampableEntity;

    private string $title;
    private string $slug;
    private string $content;
    private string $textHeader;
    private string $image;
    private string $imageAlt;
    private int $adminId;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getTextHeader(): string
    {
        return $this->textHeader;
    }

    /**
     * @param string $textHeader
     */
    public function setTextHeader(string $textHeader): void
    {
        $this->textHeader = $textHeader;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getImageAlt(): string
    {
        return $this->imageAlt;
    }

    /**
     * @param string $imageAlt
     */
    public function setImageAlt(string $imageAlt): void
    {
        $this->imageAlt = $imageAlt;
    }

    /**
     * @return int
     */
    public function getAdminId(): int
    {
        return $this->adminId;
    }

    /**
     * @param int $adminId
     */
    public function setAdminId(int $adminId)
    {
        $this->adminId = $adminId;
    }
}
