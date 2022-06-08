<?php

namespace App\Entity;

use App\Repository\MonitorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MonitorRepository::class)]
class Monitor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $Llicencia;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $TargetaSanitaria;

    #[ORM\OneToOne(targetEntity: Persona::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $Persona;

    #[ORM\ManyToMany(targetEntity: Equip::class, mappedBy: 'Monitors')]
    private $Equips;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $User;

    public function __construct()
    {
        $this->Equips = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLlicencia(): ?int
    {
        return $this->Llicencia;
    }

    public function setLlicencia(?int $Llicencia): self
    {
        $this->Llicencia = $Llicencia;

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

    /**
     * @return Collection<int, Equip>
     */
    public function getEquips(): Collection
    {
        return $this->Equips;
    }

    public function addEquip(Equip $equip): self
    {
        if (!$this->Equips->contains($equip)) {
            $this->Equips[] = $equip;
            $equip->addMonitor($this);
        }

        return $this;
    }

    public function removeEquip(Equip $equip): self
    {
        if ($this->Equips->removeElement($equip)) {
            $equip->removeMonitor($this);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }
}
