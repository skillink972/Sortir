<?php

namespace App\Entity;
use App\Entity\Campus;

class PropertySearch
{
    private Participant $user;

    private Campus $campus ;

    private ?string $motCle = null;


    private ?\DateTimeInterface $dateMin = null;


    private ?\DateTimeInterface $dateMax = null;


    private bool $organisateur = false;


    private bool $inscrit = false;


    private bool $nonInscrit = false;

    private bool $passees = false;


    /**
     * @return Participant
     */
    public function getUser(): Participant
    {
        return $this->user;
    }

    /**
     * @param Participant $user
     */
    public function setUser(Participant $user): void
    {
        $this->user = $user;
    }


    /**
     * @return Campus
     */
    public function getCampus(): Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus $campus
     */
    public function setCampus(Campus $campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return string|null
     */
    public function getMotCle(): ?string
    {
        return $this->motCle;
    }

    /**
     * @param string|null $motCle
     */
    public function setMotCle(?string $motCle): void
    {
        $this->motCle = $motCle;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateMin(): ?\DateTimeInterface
    {
        return $this->dateMin;
    }

    /**
     * @param \DateTimeInterface|null $dateMin
     */
    public function setDateMin(?\DateTimeInterface $dateMin): void
    {
        $this->dateMin = $dateMin;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateMax(): ?\DateTimeInterface
    {
        return $this->dateMax;
    }

    /**
     * @param \DateTimeInterface|null $dateMax
     */
    public function setDateMax(?\DateTimeInterface $dateMax): void
    {
        $this->dateMax = $dateMax;
    }

    /**
     * @return bool
     */
    public function isOrganisateur(): bool
    {
        return $this->organisateur;
    }

    /**
     * @param bool $organisateur
     */
    public function setOrganisateur(bool $organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    /**
     * @return bool
     */
    public function isInscrit(): bool
    {
        return $this->inscrit;
    }

    /**
     * @param bool $inscrit
     */
    public function setInscrit(bool $inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    /**
     * @return bool
     */
    public function isNonInscrit(): bool
    {
        return $this->nonInscrit;
    }

    /**
     * @param bool $nonInscrit
     */
    public function setNonInscrit(bool $nonInscrit): void
    {
        $this->nonInscrit = $nonInscrit;
    }

    /**
     * @return bool
     */
    public function isPassees(): bool
    {
        return $this->passees;
    }

    /**
     * @param bool $passees
     */
    public function setPassees(bool $passees): void
    {
        $this->passees = $passees;
    }


}