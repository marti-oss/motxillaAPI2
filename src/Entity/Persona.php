<?php

namespace App\Entity;

use App\Repository\PersonaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonaRepository::class)]
class Persona
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $Nom;

    #[ORM\Column(type: 'string', length: 255)]
    private $Cognom1;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $Cognom2;

    #[ORM\Column(type: 'string', length: 255)]
    private $DNI;

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

    public function getCognom1(): ?string
    {
        return $this->Cognom1;
    }

    public function setCognom1(string $Cognom1): self
    {
        $this->Cognom1 = $Cognom1;

        return $this;
    }

    public function getCognom2(): ?string
    {
        return $this->Cognom2;
    }

    public function setCognom2(?string $Cognom2): self
    {
        $this->Cognom2 = $Cognom2;

        return $this;
    }

    public function getDNI(): ?string
    {
        return $this->DNI;
    }

    public function setDNI(string $DNI): self
    {
        $this->DNI = $DNI;

        return $this;
    }
}
