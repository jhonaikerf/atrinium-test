<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CurrencyRepository::class)
 * @UniqueEntity("iso")
 */
class Currency
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3, unique=true)
     */
    private $iso;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=FixerLog::class, mappedBy="FromCurrency")
     */
    private $ToCurrency;

    /**
     * @ORM\OneToMany(targetEntity=FixerLog::class, mappedBy="ToCurrency")
     */
    private $fixerLogs;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    public function __construct()
    {
        $this->ToCurrency = new ArrayCollection();
        $this->fixerLogs = new ArrayCollection();
    }

    public function __toString(): ?string
    {
        return "$this->iso: $this->description";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIso(): ?string
    {
        return $this->iso;
    }

    public function setIso(string $iso): self
    {
        $this->iso = $iso;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|FixerLog[]
     */
    public function getToCurrency(): Collection
    {
        return $this->ToCurrency;
    }

    public function addToCurrency(FixerLog $toCurrency): self
    {
        if (!$this->ToCurrency->contains($toCurrency)) {
            $this->ToCurrency[] = $toCurrency;
            $toCurrency->setFromCurrency($this);
        }

        return $this;
    }

    public function removeToCurrency(FixerLog $toCurrency): self
    {
        if ($this->ToCurrency->removeElement($toCurrency)) {
            // set the owning side to null (unless already changed)
            if ($toCurrency->getFromCurrency() === $this) {
                $toCurrency->setFromCurrency(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FixerLog[]
     */
    public function getFixerLogs(): Collection
    {
        return $this->fixerLogs;
    }

    public function addFixerLog(FixerLog $fixerLog): self
    {
        if (!$this->fixerLogs->contains($fixerLog)) {
            $this->fixerLogs[] = $fixerLog;
            $fixerLog->setToCurrency($this);
        }

        return $this;
    }

    public function removeFixerLog(FixerLog $fixerLog): self
    {
        if ($this->fixerLogs->removeElement($fixerLog)) {
            // set the owning side to null (unless already changed)
            if ($fixerLog->getToCurrency() === $this) {
                $fixerLog->setToCurrency(null);
            }
        }

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
