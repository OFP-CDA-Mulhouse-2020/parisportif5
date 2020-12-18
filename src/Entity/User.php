<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as UserAssert;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(
     *     message="L'adresse email ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Email(
     *     message="L'adresse email {{ value }}' n'est pas valide",
     *     mode="html5"
     * )
     */
    private string $email;

    /**
     * @ORM\Column(type="json")
     * @var array<string> $roles
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(
     *     message="Le mot de passe ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Length(
     *     min=7,
     *     minMessage="Votre mot de passe doit avoir plus de {{ limit }} caractères",
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}]+$/u",
     *     match=false,
     *     message="Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des lettres"
     * )
     * @Assert\Regex(
     *     pattern="/^\d+$/",
     *     match=false,
     *     message="Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des chiffres"
     * )
     * @Assert\NotCompromisedPassword(
     *     message="Mot de passe interdit"
     * )
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Assert\NotBlank(
     *     message="La civilité ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Length(
     *      max = 15,
     *      maxMessage = "La civilité ne peut pas être plus longue que {{ limit }} caractères"
     * )
     */
    private string $civility;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="Le prénom ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Length(
     *     min=2,
     *     max=26,
     *     minMessage="Votre prénom doit avoir plus de {{ limit }} caractères",
     *     maxMessage="Votre prénom doit avoir moins de {{ limit }} caractères"
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\'\s]+$/u",
     *     message="Uniquement les lettres et les caractères suivant (-, ') sont autorisé"
     * )
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="Le nom de famille ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Length(
     *     min=2,
     *     max=26,
     *     minMessage="Votre nom de famille doit avoir plus de {{ limit }} caractères",
     *     maxMessage="Votre nom de famille doit avoir moins de {{ limit }} caractères"
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\'\s]+$/u",
     *     message="Uniquement les lettres et les caractères suivant (-, ') sont autorisé"
     * )
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="L'adresse ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\'\s\d\,]+$/u",
     *     message="Les caractères spéciaux ne sont pas autorisés pour l'adresse"
     * )
     */
    private string $billingAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="La ville ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\'\s]+$/u",
     *     message="Les chiffres et les caractères spéciaux ne sont pas autorisés pour la ville"
     * )
     */
    private string $billingCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="Le code postal ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\s\d]+$/u",
     *     message="Les caractères spéciaux ne sont pas autorisés pour le code postal"
     * )
     */
    private string $billingPostcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="Le pays ne peut pas être vide.",
     *     normalizer="trim"
     * )
     * @Assert\Country(
     *     message="Le pays {{ value }} n'est pas valide",
     * )
     */
    private string $billingCountry;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(
     *     message="La date de naissance ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @UserAssert\HasLegalAge
     */
    private \DateTimeImmutable $birthDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="Le fuseau horaire ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Timezone(
     *     message="Le fuseau horaire {{ value }} n'est pas valide"
     * )
     */
    private string $timeZoneSelected;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $deletedStatus;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $deletedDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $suspendedStatus;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $suspendedDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $activatedStatus;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $activatedDate;

    /**
     * @ORM\OneToOne(targetEntity=Wallet::class, inversedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private Wallet $wallet;

    /**
     * @ORM\OneToOne(targetEntity=Language::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private Language $language;

    /**
     * @var Collection<int,Bet> $onGoingBets
     * @ORM\OneToMany(targetEntity=Bet::class, mappedBy="user", orphanRemoval=true)
     * @Assert\Valid
     */
    private Collection $onGoingBets;

    /**
     * @const int MIN_AGE_FOR_BETTING
     * @Assert\Type(
     *     type="integer",
     *     message="L'âge minimum pour parier {{ value }} n'est pas du type {{ type }}."
     * )
    */
    public const MIN_AGE_FOR_BETTING = 18;

    /**
     * @const string STORED_TIME_ZONE
     * @Assert\Type(
     *     type="string",
     *     message="Le fuseau horraire stocké {{ value }} n'est pas du type {{ type }}."
     * )
     * @Assert\Timezone(
     *     message="Le fuseau horaire {{ value }} n'est pas valide"
     * )
    */
    public const STORED_TIME_ZONE = "UTC";

    /**
     * @const string SELECT_CURRENCY_SYMBOL
     * @Assert\Type(
     *     type="string",
     *     message="La devise monétaire sélectionnée {{ value }} n'est pas du type {{ type }}."
     * )
     * @Assert\Currency(
     *     message="La devise monétaire par défaut {{ value }} n'est pas valide."
     * )
    */
    public const SELECT_CURRENCY_CODE = "EUR";

    /**
     * @const string SELECT_CURRENCY_SYMBOL
     * @Assert\Type(
     *     type="string",
     *     message="Le symbole de la devise monétaire sélectionné {{ value }} n'est pas du type {{ type }}."
     * )
    */
    public const SELECT_CURRENCY_SYMBOL = "€";

    public function __construct()
    {
        $creationDate = new \DateTimeImmutable('now', new \DateTimeZone(self::STORED_TIME_ZONE));
        $this->activatedStatus = true;
        $this->activatedDate = $creationDate;
        $this->suspendedStatus = true;
        $this->suspendedDate = $creationDate;
        $this->deletedStatus = false;
        $this->deletedDate = null;
        $this->onGoingBets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /** @param array<string> $roles */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @Assert\IsTrue(
     *     message="Le mot de passe ne doit pas contenir le prénom et/ou le nom"
     * )
     */
    public function isPasswordSafe(): bool
    {
        return (stripos($this->password, $this->lastName) === false
            && stripos($this->password, $this->firstName) === false);
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCivility(): ?string
    {
        return $this->civility;
    }

    public function setCivility(string $civility): self
    {
        $this->civility = $civility;
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

    public function getFullName(): ?string
    {
        $fullName = trim(($this->firstName ?? '') . ' ' . ($this->lastName ?? ''));
        return !empty($fullName) ? $fullName : null;
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

    public function getBillingCity(): ?string
    {
        return $this->billingCity;
    }

    public function setBillingCity(string $billingCity): self
    {
        $this->billingCity = $billingCity;
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

    public function getFullAddress(): ?string
    {
        $fullAddress = trim(($this->billingAddress ?? '') . ' ' .
            ($this->billingPostcode ?? '') . ' ' . ($this->billingCity ?? '') . ' ' .
            ($this->billingCountry ?? ''));
        return !empty($fullAddress) ? $fullAddress : null;
    }

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate): self
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getTimeZoneSelected(): ?string
    {
        return $this->timeZoneSelected;
    }

    public function setTimeZoneSelected(string $timeZoneSelected): self
    {
        $this->timeZoneSelected = $timeZoneSelected;
        return $this;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(Wallet $wallet): self
    {
        $this->wallet = $wallet;
        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function getDeletedStatus(): ?bool
    {
        return $this->deletedStatus;
    }

    public function getDeletedDate(): ?\DateTimeImmutable
    {
        return $this->deletedDate;
    }

    public function delete(): bool
    {
        if (empty($this->deletedDate) && $this->deletedStatus === false) {
            $this->deletedDate = new \DateTimeImmutable('now', new \DateTimeZone(self::STORED_TIME_ZONE));
            $this->deletedStatus = true;
            $this->activatedStatus = false;
            $this->suspendedStatus = true;
            return true;
        }
        return false;
    }

    public function restore(): bool
    {
        if (!empty($this->deletedDate) && $this->deletedStatus === true) {
            $this->deletedDate = null;
            $this->deletedStatus = false;
            $this->activatedStatus = !empty($this->ActivatedDate);
            $this->suspendedStatus = !empty($this->SuspendedDate);
            return true;
        }
        return false;
    }

    public function getSuspendedStatus(): ?bool
    {
        return $this->suspendedStatus;
    }

    public function getSuspendedDate(): ?\DateTimeImmutable
    {
        return $this->suspendedDate;
    }

    public function suspend(): bool
    {
        if ($this->activatedStatus === true && empty($this->suspendedDate) && $this->suspendedStatus === false) {
            $this->suspendedDate = new \DateTimeImmutable('now', new \DateTimeZone(self::STORED_TIME_ZONE));
            $this->suspendedStatus = true;
            return true;
        }
        return false;
    }

    public function valid(): bool
    {
        if ($this->activatedStatus === true && !empty($this->suspendedDate) && $this->suspendedStatus === true) {
            $this->suspendedDate = null;
            $this->suspendedStatus = false;
            return true;
        }
        return false;
    }

    public function getActivatedStatus(): ?bool
    {
        return $this->activatedStatus;
    }

    public function getActivatedDate(): ?\DateTimeImmutable
    {
        return $this->activatedDate;
    }

    public function activate(): bool
    {
        if (empty($this->activatedDate) && $this->activatedStatus === false) {
            $this->activatedDate = new \DateTimeImmutable('now', new \DateTimeZone(self::STORED_TIME_ZONE));
            $this->activatedStatus = true;
            return true;
        }
        return false;
    }

    public function desactivate(): bool
    {
        if (!empty($this->activatedDate) && $this->activatedStatus === true) {
            $this->activatedDate = null;
            $this->activatedStatus = false;
            return true;
        }
        return false;
    }

    /**
     * @return Collection<int,Bet>
     */
    public function getOnGoingBets(): Collection
    {
        return $this->onGoingBets;
    }

    public function addOnGoingBet(Bet $onGoingBet): self
    {
        if (!$this->onGoingBets->contains($onGoingBet)) {
            $this->onGoingBets[] = $onGoingBet;
            //$onGoingBet->setUser($this);
        }

        return $this;
    }

    public function removeOnGoingBet(Bet $onGoingBet): self
    {
        if ($this->onGoingBets->removeElement($onGoingBet)) {
            // set the owning side to null (unless already changed)
            /*if ($onGoingBet->getUser() === $this) {
                $onGoingBet->setUser(null);
            }*/
        }

        return $this;
    }
}
