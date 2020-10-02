<?php

namespace App\Entity;

class Admin extends User
{
    private int $id;
    private string $image;
    private string $altImg;
    private string $cvLink;
    private string $shortDescription;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
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
    public function getAltImg(): string
    {
        return $this->altImg;
    }

    /**
     * @param string $altImg
     */
    public function setAltImg(string $altImg): void
    {
        $this->altImg = $altImg;
    }

    /**
     * @return string
     */
    public function getCvLink(): string
    {
        return $this->cvLink;
    }

    /**
     * @param string $cvLink
     */
    public function setCvLink(string $cvLink): void
    {
        $this->cvLink = $cvLink;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     */
    public function setShortDescription(string $shortDescription): void
    {
        $this->shortDescription = $shortDescription;
    }
}
