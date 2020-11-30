<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Exception\AccountNotActiveException;
use App\Entity\Exception\BoundaryDateException;
use App\Entity\Exception\LegalAgeException;
use App\Entity\Exception\UnknownTimeZoneException;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $civility;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $billingAddress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $billingPostcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $billingCountry;

    /**
     * @ORM\Column(type="date")
     */
    private \DateTime $birthDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $emailAddress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $timeZoneSelected;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $deletedStatus;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $deletedDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $suspendedStatus;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $suspendedDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $activatedStatus;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $activatedDate;

    /**
     * @var array<int, string>
     */
    private array $timezoneIdentifiersList = [];
    public const MIN_AGE_FOR_BETTING = 18;

    public function __construct()
    {
        $this->timezoneIdentifiersList = \DateTimeZone::listIdentifiers();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCivility(): ?string
    {
        return $this->civility;
    }

    public function setCivility(string $civility): self
    {
        if ($civility != "Monsieur" && $civility != "Madame") {
            throw new \InvalidArgumentException("La civilité doit être renseignée 
                et être l'un des deux termes suivants : Monsieur ou Madame");
        }

        $this->civility = $civility;

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): self
    {
        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException("L'adresse email est invalide");
        }

        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(string $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getBillingPostcode(): ?string
    {
        return $this->billingPostcode;
    }

    public function setBillingPostcode(string $billingPostcode): self
    {
        $this->billingPostcode = $billingPostcode;

        return $this;
    }

    public function getBillingCountry(): ?string
    {
        return $this->billingCountry;
    }

    public function setBillingCountry(string $billingCountry): self
    {
        $this->billingCountry = $billingCountry;

        return $this;
    }

    public function getBirthDate(): ?\DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTime $birthDate): self
    {
        //$birthDateTimeZone = $birthDate->getTimezone();
        //$timeZone = $birthDateTimeZone !== false ?
        //    $birthDateTimeZone->getName() : ($this->timeZoneSelected ?? 'UTC');
        $timeZoneString = $this->timeZoneSelected ?? 'UTC';
        $timeZoneObject = new \DateTimeZone($timeZoneString);
        $birthDate = $birthDate->setTimezone($timeZoneObject);
        $currentDate = new \DateTime('now', $timeZoneObject);
        if ($birthDate >= $currentDate) {
            throw new BoundaryDateException("La date de naissance ne peut être supérieur 
                ou égal à la date en cours.");
        }
        $currentDate = $currentDate->setTime(23, 59, 59, 999999);
        $legalAge = clone $birthDate;
        $legalAge = $legalAge->setTime(23, 59, 60);
        $legalAge->add(new \DateInterval('P' . self::MIN_AGE_FOR_BETTING . 'Y'));
        if ($legalAge > $currentDate) {
            throw new LegalAgeException("L'âge requis pour créer un compte est de 
                " . self::MIN_AGE_FOR_BETTING . " ans.");
        }

        $this->birthDate = $birthDate;

        return $this;
    }

    public function getTimeZoneSelected(): ?string
    {
        return $this->timeZoneSelected;
    }

    public function setTimeZoneSelected(string $timeZoneSelected): self
    {
        if (in_array($timeZoneSelected, $this->timezoneIdentifiersList) == false) {
            throw new UnknownTimeZoneException("Le fuseu horaire n'est pas reconnu.");
        }

        $this->timeZoneSelected = $timeZoneSelected;

        return $this;
    }

    public function getDeletedStatus(): ?bool
    {
        return $this->deletedStatus;
    }

    public function setDeletedStatus(bool $deletedStatus): self
    {
        $this->deletedStatus = $deletedStatus;

        return $this;
    }

    public function getDeletedDate(): ?\DateTime
    {
        return $this->deletedDate;
    }

    public function setDeletedDate(?\DateTime $deletedDate): self
    {
        if ($deletedDate instanceof \DateTime) {
            $timeZoneString = $this->timeZoneSelected ?? 'UTC';
            $timeZoneObject = new \DateTimeZone($timeZoneString);
            $deletedDate = $deletedDate->setTimezone($timeZoneObject);
            $currentDate = new \DateTime('now', $timeZoneObject);
            if ($deletedDate > $currentDate) {
                throw new BoundaryDateException("La date de suppression du compte ne peut être supérieur 
                    à la date en cours.");
            }
        }

        $this->deletedDate = $deletedDate;

        return $this;
    }

    public function getSuspendedStatus(): ?bool
    {
        return $this->suspendedStatus;
    }

    public function setSuspendedStatus(bool $suspendedStatus): self
    {
        if ($this->getActivatedStatus() !== true) {
            throw new AccountNotActiveException("Le compte ne peut pas être suspendu 
                si il n'est pas actif.");
        }

        $this->suspendedStatus = $suspendedStatus;

        return $this;
    }

    public function getSuspendedDate(): ?\DateTime
    {
        return $this->suspendedDate;
    }

    public function setSuspendedDate(?\DateTime $suspendedDate): self
    {
        if ($this->getActivatedStatus() !== true) {
            throw new AccountNotActiveException("La date de suspension du compte ne peut être modifier 
                si le compte n'est pas actif.");
        }
        if ($suspendedDate instanceof \DateTime) {
            $timeZoneString = $this->timeZoneSelected ?? 'UTC';
            $timeZoneObject = new \DateTimeZone($timeZoneString);
            $suspendedDate = $suspendedDate->setTimezone($timeZoneObject);
            $currentDate = new \DateTime('now', $timeZoneObject);
            if ($suspendedDate > $currentDate) {
                throw new BoundaryDateException("La date de suspension du compte ne peut être supérieur 
                    à la date en cours.");
            }
        }

        $this->suspendedDate = $suspendedDate;

        return $this;
    }

    public function getActivatedStatus(): ?bool
    {
        return $this->activatedStatus;
    }

    public function setActivatedStatus(bool $activatedStatus): self
    {
        $this->activatedStatus = $activatedStatus;

        return $this;
    }

    public function getActivatedDate(): ?\DateTime
    {
        return $this->activatedDate;
    }

    public function setActivatedDate(?\DateTime $activatedDate): self
    {
        if ($activatedDate instanceof \DateTime) {
            $timeZoneString = $this->timeZoneSelected ?? 'UTC';
            $timeZoneObject = new \DateTimeZone($timeZoneString);
            $activatedDate = $activatedDate->setTimezone($timeZoneObject);
            $currentDate = new \DateTime('now', $timeZoneObject);
            if ($activatedDate > $currentDate) {
                throw new BoundaryDateException("La date d'activation du compte ne peut être supérieur 
                    à la date en cours.");
            }
        }

        $this->activatedDate = $activatedDate;

        return $this;
    }
}
