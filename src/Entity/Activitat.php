<?php

namespace App\Entity;

use App\Repository\ActivitatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivitatRepository::class)]
class Activitat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $Nom;

    #[ORM\Column(type: 'string', length: 255)]
    private $Objectiu;

    #[ORM\Column(type: 'boolean')]
    private $Interior;

    #[ORM\Column(type: 'text', nullable: true)]
    private $Descripcio;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getObjectiu(): ?string
    {
        return $this->Objectiu;
    }

    public function setObjectiu(string $Objectiu): self
    {
        $this->Objectiu = $Objectiu;

        return $this;
    }

    public function isInterior(): ?bool
    {
        return $this->Interior;
    }

    public function setInterior(bool $Interior): self
    {
        $this->Interior = $Interior;

        return $this;
    }

    public function getDescripcio(): ?string
    {
        return $this->Descripcio;
    }

    public function setDescripcio(?string $Descripcio): self
    {
        $this->Descripcio = $Descripcio;

        return $this;
    }
}
