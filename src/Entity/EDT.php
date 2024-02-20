<?php

namespace App\Entity;

use App\Repository\EDTRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EDTRepository::class)]
class EDT
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sejour $id_sejour = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $id_medecin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getIdSejour(): ?Sejour
    {
        return $this->id_sejour;
    }

    public function setIdSejour(?Sejour $id_sejour): static
    {
        $this->id_sejour = $id_sejour;

        return $this;
    }

    public function getIdMedecin(): ?Utilisateur
    {
        return $this->id_medecin;
    }

    public function setIdMedecin(?Utilisateur $id_medecin): static
    {
        $this->id_medecin = $id_medecin;

        return $this;
    }
}
