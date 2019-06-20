<?php

namespace ruano_a\AccessLimiterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * FailAccessAttempt
 *
 * @ORM\Table(name="AccessLimiter_FailAccessAttempt")
 * @ORM\Entity(repositoryClass="ruano_a\AccessLimiterBundle\Repository\FailAccessAttemptRepository")
 */
class FailAccessAttempt
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255, nullable=false)
     */
    private $ip;

    /**
     * @var int
     *
     * @ORM\Column(name="nbFails", type="integer")
     */
    private $nbFails = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastFailDate", type="datetime")
     */
    private $lastFailDate;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getNbFails(): ?int
    {
        return $this->nbFails;
    }

    public function setNbFails(int $nbFails): self
    {
        $this->nbFails = $nbFails;

        return $this;
    }

    public function addFail(): self
    {
        $this->nbFails++;

        return $this;
    }

    public function getLastFailDate(): ?\DateTimeInterface
    {
        return $this->lastFailDate;
    }

    public function setLastFailDate(\DateTimeInterface $lastFailDate): self
    {
        $this->lastFailDate = $lastFailDate;

        return $this;
    }

    public function initLastFailDate(): self
    {
        $this->lastFailDate = new \DateTime();

        return $this;
    }
}
