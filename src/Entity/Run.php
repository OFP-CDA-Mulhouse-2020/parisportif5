<?php

namespace App\Entity;

use App\Repository\RunRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RunRepository::class)
 */
class Run
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le nom de la course ou du match ne peut pas être vide",
     *     normalizer="trim"
     * )
     */
    private string $name;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\GreaterThanOrEqual(
     *     value="tomorrow UTC",
     *     message="La date du début de la course ou du match doit être supérieur ou égale au {{ compared_value }} UTC"
     * )
     */
    private \DateTimeImmutable $startDate;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\GreaterThan(
     *     propertyPath="startDate",
     *     message="La date de fin de la course ou du match doit être supérieur à la date du début de celle-ci ({{ compared_value }} UTC)"
     * )
     */
    private \DateTimeImmutable $endDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function isFinish(): bool
    {
        $timezoneUTC = new \DateTimeZone('UTC');
        $currentDate = new \DateTime('now', $timezoneUTC);
        return ($currentDate > $this->endDate->setTimezone($timezoneUTC));
    }

    public function isOngoing(): bool
    {
        $timezoneUTC = new \DateTimeZone('UTC');
        $currentDate = new \DateTime('now', $timezoneUTC);
        return ($currentDate >= $this->startDate->setTimezone($timezoneUTC)
            && $currentDate <= $this->endDate->setTimezone($timezoneUTC));
    }
}
