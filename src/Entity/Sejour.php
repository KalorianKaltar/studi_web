<?php

namespace App\Entity;

use App\Repository\SejourRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SejourRepository::class)]
class Sejour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $id_patient = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 255)]
    private ?string $motif = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Specialite $id_specialite = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $id_medecin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPatient(): ?Utilisateur
    {
        return $this->id_patient;
    }

    public function setIdPatient(?Utilisateur $id_patient): static
    {
        $this->id_patient = $id_patient;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getIdSpecialite(): ?Specialite
    {
        return $this->id_specialite;
    }

    public function setIdSpecialite(?Specialite $id_specialite): static
    {
        $this->id_specialite = $id_specialite;

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
