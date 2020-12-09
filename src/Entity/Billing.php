<?php

namespace App\Entity;

use App\Entity\Exception\BillingBlankAddressException;
use App\Entity\Exception\BillingInvalidNameException;
use App\Entity\Exception\BillingBlankNameException;
use App\Entity\Exception\BillingInvalidAddressException;
use App\Entity\Exception\BillingNameLengthException;
use App\Repository\BillingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BillingRepository::class)
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
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $postcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $country;

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
        if (trim($firstName) == "") {
            throw new BillingBlankNameException("Le prénom ne peut être vide");
        }
        if (preg_match('/^[\p{L}\-\'\s]+$/u', $firstName) !== 1) {
            throw new BillingInvalidNameException("Le prénom ne peut contenir que des lettres, des apostrophes (') et des tirets");
        }
        if ((mb_strlen($firstName) < 2) || (mb_strlen($firstName) > 25)) {
            throw new BillingNameLengthException("Le prénom ne peut être inférieur à 2 caractère et supérieur à 25 caractères");
        }
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        if (trim($lastName) == "") {
            throw new BillingBlankNameException("Le nom de famille ne peut être vide");
        }
        if (preg_match('/^[\p{L}\-\'\s]+$/u', $lastName) !== 1) {
            throw new BillingInvalidNameException("Le nom de famille ne peut contenir que des lettres, des apostrophes (') et des tirets");
        }
        if ((mb_strlen($lastName) < 2) || (mb_strlen($lastName) > 25)) {
            throw new BillingNameLengthException("Le nom de famille ne peut être inférieur à 2 caractère et supérieur à 25 caractères");
        }
        $this->lastName = $lastName;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        if (trim($address) == "") {
            throw new BillingBlankAddressException("L'adresse ne peut être vide");
        }
        if (preg_match('/^[\p{L}\-\'\s\d]+$/u', $address) !== 1) {
            throw new BillingInvalidAddressException("L'adresse ne peut contenir que des lettres, des chiffres, des apostrophes (') et des tirets");
        }
        $this->address = $address;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        if (trim($city) == "") {
            throw new BillingBlankAddressException("La ville ne peut être vide");
        }
        if (preg_match('/^[\p{L}\-\s]+$/u', $city) !== 1) {
            throw new BillingInvalidAddressException("La ville ne peut contenir que des lettres et des tirets");
        }
        $this->city = $city;
        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): self
    {
        if (trim($postcode) == "") {
            throw new BillingBlankAddressException("Le code postale ne peut être vide");
        }
        if (preg_match('/^[a-z\-\s\d]+$/i', $postcode) !== 1) {
            throw new BillingInvalidAddressException("Le code postale ne peut contenir que des lettres (sans accents), des chiffres et des tirets");
        }
        $this->postcode = $postcode;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        if (trim($country) == "") {
            throw new BillingBlankAddressException("Le pays ne peut être vide");
        }
        if (preg_match('/^[\p{L}]+$/u', $country) !== 1) {
            throw new BillingInvalidAddressException("Le pays ne peut contenir que des lettres");
        }
        $this->country = $country;
        return $this;
    }
}
