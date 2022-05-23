<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'boolean')]
    private $Autoritzacio;

    #[ORM\Column(type: 'date')]
    private $DataNaixement;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $TargetaSanitaria;

    #[ORM\OneToOne(targetEntity: Persona::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $Persona;

    #[ORM\ManyToOne(targetEntity: Equip::class, inversedBy: 'Participants')]
    private $Equip;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isAutoritzacio(): ?bool
    {
        return $this->Autoritzacio;
    }

    public function setAutoritzacio(bool $Autoritzacio): self
    {
        $this->Autoritzacio = $Autoritzacio;

        return $this;
    }

    public function getDataNaixement(): ?\DateTimeInterface
    {
        return $this->DataNaixement;
    }

    public function setDataNaixement(\DateTimeInterface $DataNaixement): self
    {
        $this->DataNaixement = $DataNaixement;

        return $this;
    }

    public function getTargetaSanitaria(): ?string
    {
        return $this->TargetaSanitaria;
    }

    public function setTargetaSanitaria(?string $TargetaSanitaria): self
    {
        $this->TargetaSanitaria = $TargetaSanitaria;

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

    public function getEquip(): ?Equip
    {
        return $this->Equip;
    }

    public function setEquip(?Equip $Equip): self
    {
        $this->Equip = $Equip;

        return $this;
    }
}
