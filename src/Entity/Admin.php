<?php

namespace App\Entity;

class Admin extends User
{
    private ?string $image = null;
    private ?string $altImg = null;
    private ?string $cvLink = null;
    private ?string $shortDescription = null;
    private int    $userId;

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     * @return Admin
     */
    public function setImage(?string $image): Admin
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAltImg(): ?string
    {
        return $this->altImg;
    }

    /**
     * @param string|null $altImg
     * @return Admin
     */
    public function setAltImg(?string $altImg): Admin
    {
        $this->altImg = $altImg;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCvLink(): ?string
    {
        return $this->cvLink;
    }

    /**
     * @param string|null $cvLink
     * @return Admin
     */
    public function setCvLink(?string $cvLink): Admin
    {
        $this->cvLink = $cvLink;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    /**
     * @param string|null $shortDescription
     * @return Admin
     */
    public function setShortDescription(?string $shortDescription): Admin
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return Admin
     */
    public function setUserId(int $userId): Admin
    {
        $this->userId = $userId;

        return $this;
    }

}
