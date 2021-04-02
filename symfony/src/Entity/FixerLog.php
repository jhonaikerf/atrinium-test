<?php

namespace App\Entity;

use App\Repository\FixerLogRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FixerLogRepository::class)
 */
class FixerLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $toAmount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $fromAmount;

    /**
     * @ORM\ManyToOne(targetEntity=Currency::class, inversedBy="fixerLogs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fromCurrency;

    /**
     * @ORM\ManyToOne(targetEntity=Currency::class, inversedBy="fixerLogs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $toCurrency;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $rate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromAmount(): ?string
    {
        return $this->fromAmount;
    }

    public function setFromAmount(string $fromAmount): self
    {
        $this->fromAmount = $fromAmount;

        return $this;
    }

    public function getToAmount(): ?string
    {
        return $this->toAmount;
    }

    public function setToAmount(?string $toAmount): self
    {
        $this->toAmount = $toAmount;

        return $this;
    }

    public function getFromCurrency(): ?Currency
    {
        return $this->fromCurrency;
    }

    public function setFromCurrency(?Currency $fromCurrency): self
    {
        $this->fromCurrency = $fromCurrency;

        return $this;
    }

    public function getToCurrency(): ?Currency
    {
        return $this->toCurrency;
    }

    public function setToCurrency(?Currency $toCurrency): self
    {
        $this->toCurrency = $toCurrency;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): self
    {
        $this->rate = $rate;

        return $this;
    }
}
