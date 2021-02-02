<?php

declare(strict_types=1);

namespace App\Entity;

use App\DataConverter\DateTimeStorageInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BillingRepository;

/**
 * @ORM\Entity(repositoryClass=BillingRepository::class)
 * @UniqueEntity(
 *     fields="invoiceNumber",
 *     message="Cette facture est déjà enregistrée."
 * )
 */
class Billing
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
     *     message="Le prénom ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Length(
     *     min=2,
     *     max=25,
     *     minMessage="Votre prénom doit avoir au moins {{ limit }} caractères.",
     *     maxMessage="Votre prénom ne doit pas avoir plus de {{ limit }} caractères."
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\'\s]+$/u",
     *     message="Les chiffres et les caractères spéciaux ne sont pas autorisés pour le prénom"
     * )
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le nom de famille ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Length(
     *     min=2,
     *     max=25,
     *     minMessage="Votre nom de famille doit avoir au moins {{ limit }} caractères.",
     *     maxMessage="Votre nom de famille ne doit pas avoir plus de {{ limit }} caractères."
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\'\s]+$/u",
     *     message="Les chiffres et les caractères spéciaux ne sont pas autorisés pour le nom de famille"
     * )
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="L'adresse ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\'\s\d\,]+$/u",
     *     message="Les caractères spéciaux ne sont pas autorisés pour l'adresse"
     * )
     */
    private string $address;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="La ville ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\'\s]+$/u",
     *     message="Les chiffres et les caractères spéciaux ne sont pas autorisés pour la ville"
     * )
     */
    private string $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le code postal ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\s\d]+$/u",
     *     message="Les caractères spéciaux ne sont pas autorisés pour le code postal"
     * )
     */
    private string $postcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le pays ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Country(
     *     message="Le pays {{ value }} n'est pas valide",
     * )
     */
    private string $country;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="La désignation de la facture ne peut pas être vide",
     *     normalizer="trim"
     * )
     */
    private string $designation;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(
     *     message="Le numéro de commande doit être un entier positif"
     * )
     */
    private int $orderNumber;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(
     *     message="Le numéro de facture doit être un entier positif"
     * )
     */
    private int $invoiceNumber;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(
     *     message="La montant de la facture en centimes doit être un entier positif ou zéro"
     * )
     */
    private int $amount;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(
     *     message="Le taux de commission (multiplier par 10000) doit être un entier positif ou zéro"
     * )
     */
    private int $commissionRate;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $issueDate;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $deliveryDate;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private ?User $user = null;

    /**
     * @ORM\Column(type="string", length=6)
     * @Assert\NotBlank(
     *     message="Le type d'opération ne peut pas être vide.",
     *     normalizer="trim"
     * )
     * @Assert\Choice(
     *     choices=Billing::OPERATION_TYPES,
     *     message="Choisisez une opération valide."
     * )
     */
    private string $operationType;

    /** Sécurise le stockage des dates et heures */
    private DateTimeStorageInterface $dateTimeConverter;

    /**
     * @const float DEFAULT_COMMISSION_RATE
     * @Assert\Type(
     *     type="float",
     *     message="Le taux de commission par défaut {{ value }} n'est pas du type {{ type }}."
     * )
    */
    public const DEFAULT_COMMISSION_RATE =  7.5;

    /**
     * @const string DEFAULT_CURRENCY_NAME
     * @Assert\Type(
     *     type="string",
     *     message="La devise monétaire par défaut {{ value }} n'est pas du type {{ type }}."
     * )
     * @Assert\Currency(
     *     message="La devise monétaire par défaut {{ value }} n'est pas valide."
     * )
    */
    public const DEFAULT_CURRENCY_CODE = "EUR";

    /**
     * @const string DEFAULT_CURRENCY_SYMBOL
     * @Assert\Type(
     *     type="string",
     *     message="Le symbole de la devise monétaire par défaut {{ value }} n'est pas du type {{ type }}."
     * )
    */
    public const DEFAULT_CURRENCY_SYMBOL = "€";

    /** @const string CREDIT */
    public const CREDIT = "credit";

    /** @const string DEBIT */
    public const DEBIT = "debit";

    /**
     * @const string[] OPERATION_TYPES
    */
    public const OPERATION_TYPES = [self::DEBIT, self::CREDIT];

    public function __construct(DateTimeStorageInterface $dateTimeConverter)
    {
        $this->dateTimeConverter = $dateTimeConverter;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getFullAddress(): ?string
    {
        $fullAddress = trim(($this->address ?? '') . ' ' .
            ($this->postcode ?? '') . ' ' . ($this->city ?? '') . ' ' .
            ($this->country ?? ''));
        return !empty($fullAddress) ? $fullAddress : null;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;
        return $this;
    }

    public function getOrderNumber(): ?int
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(int $orderNumber): self
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getInvoiceNumber(): ?int
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(int $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getIssueDate(): ?\DateTimeImmutable
    {
        return $this->issueDate;
    }

    public function setIssueDate(\DateTimeInterface $issueDate): self
    {
        $issueDate = $this->dateTimeConverter->convertedToStoreDateTime($issueDate);
        $this->issueDate = $issueDate;
        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeImmutable
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTimeInterface $deliveryDate): self
    {
        $deliveryDate = $this->dateTimeConverter->convertedToStoreDateTime($deliveryDate);
        $this->deliveryDate = $deliveryDate;
        return $this;
    }

    public function getCommissionRate(): ?int
    {
        return $this->commissionRate;
    }

    public function setCommissionRate(int $commissionRate): self
    {
        $this->commissionRate = $commissionRate;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getOperationType(): ?string
    {
        return $this->operationType;
    }

    public function setOperationType(string $operationType): self
    {
        if (in_array($operationType, self::OPERATION_TYPES) !== false) {
            $this->operationType = $operationType;
        }

        return $this;
    }

    public function hasUser(): bool
    {
        return empty($this->user) ? false : true;
    }

    public function setDateTimeConverter(DateTimeStorageInterface $dateTimeConverter): self
    {
        $this->dateTimeConverter = $dateTimeConverter;

        return $this;
    }
}
