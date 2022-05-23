<?php

namespace App\Entity;

use App\Repository\EquipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipRepository::class)]
class Equip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $Nom;

    #[ORM\ManyToMany(targetEntity: Monitor::class, inversedBy: 'Equips')]
    private $Monitors;

    #[ORM\OneToMany(mappedBy: 'Equip', targetEntity: Participant::class)]
    private $Participants;

    #[ORM\OneToMany(mappedBy: 'Equip', targetEntity: ActivitatProgramada::class)]
    private $ActivitatsProgramades;

    public function __construct()
    {
        $this->Monitors = new ArrayCollection();
        $this->Participants = new ArrayCollection();
        $this->ActivitatsProgramades = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Monitor>
     */
    public function getMonitors(): Collection
    {
        return $this->Monitors;
    }

    public function addMonitor(Monitor $monitor): self
    {
        if (!$this->Monitors->contains($monitor)) {
            $this->Monitors[] = $monitor;
        }

        return $this;
    }

    public function removeMonitor(Monitor $monitor): self
    {
        $this->Monitors->removeElement($monitor);

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->Participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->Participants->contains($participant)) {
            $this->Participants[] = $participant;
            $participant->setEquip($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->Participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getEquip() === $this) {
                $participant->setEquip(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ActivitatProgramada>
     */
    public function getActivitatsProgramades(): Collection
    {
        return $this->ActivitatsProgramades;
    }

    public function addActivitatsProgramade(ActivitatProgramada $activitatsProgramade): self
    {
        if (!$this->ActivitatsProgramades->contains($activitatsProgramade)) {
            $this->ActivitatsProgramades[] = $activitatsProgramade;
            $activitatsProgramade->setEquip($this);
        }

        return $this;
    }

    public function removeActivitatsProgramade(ActivitatProgramada $activitatsProgramade): self
    {
        if ($this->ActivitatsProgramades->removeElement($activitatsProgramade)) {
            // set the owning side to null (unless already changed)
            if ($activitatsProgramade->getEquip() === $this) {
                $activitatsProgramade->setEquip(null);
            }
        }

        return $this;
    }
}
