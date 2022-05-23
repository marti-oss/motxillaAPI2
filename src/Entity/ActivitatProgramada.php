<?php

namespace App\Entity;

use App\Repository\ActivitatProgramadaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivitatProgramadaRepository::class)]
class ActivitatProgramada
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

    #[ORM\Column(type: 'datetime')]
    private $DataIni;

    #[ORM\Column(type: 'datetime')]
    private $DataFi;

    #[ORM\ManyToOne(targetEntity: Equip::class, inversedBy: 'ActivitatsProgramades')]
    #[ORM\JoinColumn(nullable: false)]
    private $Equip;

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

    public function getDataIni(): ?\DateTimeInterface
    {
        return $this->DataIni;
    }

    public function setDataIni(\DateTimeInterface $DataIni): self
    {
        $this->DataIni = $DataIni;

        return $this;
    }

    public function getDataFi(): ?\DateTimeInterface
    {
        return $this->DataFi;
    }

    public function setDataFi(\DateTimeInterface $DataFi): self
    {
        $this->DataFi = $DataFi;

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
