<?php

namespace App\Entity;

use App\Core\Entity;

class Article extends Entity
{
    private string $title;
    private string $content;
    private string $textHeader;

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
}
