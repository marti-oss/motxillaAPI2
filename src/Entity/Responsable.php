<?php

namespace App\Entity;

use App\Repository\ResponsableRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponsableRepository::class)]
class Responsable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $Telefon1;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $Telefon2;

    #[ORM\Column(type: 'string', length: 255)]
    private $Email;

    #[ORM\OneToOne(targetEntity: Persona::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $Persona;

    #[ORM\OneToOne(targetEntity: Participant::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $Participant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTelefon1(): ?int
    {
        return $this->Telefon1;
    }

    public function setTelefon1(int $Telefon1): self
    {
        $this->Telefon1 = $Telefon1;

        return $this;
    }

    public function getTelefon2(): ?int
    {
        return $this->Telefon2;
    }

    public function setTelefon2(?int $Telefon2): self
    {
        $this->Telefon2 = $Telefon2;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    public function getPersona(): ?Persona
    {
        return $this->Persona;
    }

    public function setPersona(Persona $Persona): self
    {
        $this->Persona = $Persona;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->Participant;
    }

    public function setParticipant(Participant $Participant): self
    {
        $this->Participant = $Participant;

        return $this;
    }
}
