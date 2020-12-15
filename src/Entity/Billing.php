<?php

namespace App\Entity;

use App\Repository\BillingRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BillingRepository::class)
 */
class Billing implements FundStorageInterface
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
     *     max=26,
     *     minMessage="Votre prénom doit avoir plus de {{ limit }} caractères",
     *     maxMessage="Votre prénom doit avoir moins de {{ limit }} caractères"
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
     *     max=26,
     *     minMessage="Votre nom de famille doit avoir plus de {{ limit }} caractères",
     *     maxMessage="Votre nom de famille doit avoir moins de {{ limit }} caractères"
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
     * @Assert\Type(
     *     type="integer",
     *     message="La valeur {{ value }} n'est pas du type {{ type }}."
     * )
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
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $issueDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $deliveryDate;

    /**
     * @const int DEFAULT_COMMISSION_RATE
     * @Assert\Type(
     *     type="integer",
     *     message="Le taux de commission par défaut {{ value }} n'est pas du type {{ type }}."
     * )
    */
    public const DEFAULT_COMMISSION_RATE =  75000;

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

    public function getIssueDate(): ?\DateTimeInterface
    {
        return $this->issueDate;
    }

    public function setIssueDate(\DateTimeInterface $issueDate): self
    {
        $this->issueDate = $issueDate;
        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTimeInterface $deliveryDate): self
    {
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

    public function convertToCurrencyUnit(int $amount): float
    {
        return ((float)$amount * 0.01);
    }

    public function convertToCommissionRate(int $commissionRate): float
    {
        return ((float)$commissionRate * 0.0001);
    }

    public function convertCurrencyUnitToStoredData(float $amount): int
    {
        return intVal($amount * 100);
    }

    public function convertCommissionRateToStoredData(float $commissionRate): int
    {
        return intVal($commissionRate * 10000);
    }
}
