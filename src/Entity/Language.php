<?php

namespace App\Entity;

use App\Repository\LanguageRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LanguageRepository::class)
 */
class Language
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
     *     message="Le nom du langage ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\s]+$/u",
     *     message="Les chiffres et les caractères spéciaux ne sont pas autorisés pour le nom du langage"
     * )
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le pays du langage ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\s]+$/u",
     *     message="Les chiffres et les caractères spéciaux ne sont pas autorisés pour le pays du langage"
     * )
     */
    private string $country;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(
     *     message="Le code du langage ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Locale(
     *     message="Le code du langage {{ value }} n'est pas valide (identifiants locaux au format ICU)"
     * )
     */
    private string $code;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(
     *     message="Le format de la date ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^([Dl]\,\s)?(([dj](\-|\s|\/)[mn](\-|\s|\/)Y)|(Y(\-|\s|\/)[mn](\-|\s|\/)[dj])|([mn](\-|\s|\/)[dj](\-|\s|\/)Y))\s?$/u",
     *     message="Certains caractères spéciaux et paramètres sur la date ne sont pas autorisés pour le format de la date"
     * )
     */
    private string $dateFormat;
    // pattern="/[\p{C}\p{Pi}\p{Pf}\p{No}\p{S}]+/u",

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(
     *     message="Le format de l'heure ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^(\\T)?[HG](\:|\s|(\s?(\\\p{Ll}|\\\p{Lu})+\s))i(\:|\s|(\s?(\\\p{Ll}|\\\p{Lu})+\s))s(\.u|\.vP?|P|O|\sO|\s?(\\\p{Ll}|\\\p{Lu})+)?(\s?T)?$/u",
     *     message="Certains caractères spéciaux et paramètres sur l'heure ne sont pas autorisés pour le format de l'heure"
     * )
     */
    private string $timeFormat;
    // pattern="/^(\\T)?[HG](\:|\s)i(\:|\s)s(\.u|\.vP?|P|O|\sO)?(\s?T)?$/u",
    // pattern="/[\p{C}\p{Pi}\p{Pf}\p{No}\p{Sc}\p{Sk}\p{So}]+/",

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }

    public function setDateFormat(string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    public function getTimeFormat(): ?string
    {
        return $this->timeFormat;
    }

    public function setTimeFormat(string $timeFormat): self
    {
        $this->timeFormat = $timeFormat;
        return $this;
    }

    /**
     * @Assert\IsTrue(
     *     message="Le format de la date et de l'heure doit être valide"
     * )
     */
    public function isValidDateTimeFormat(): bool
    {
        $datetime = new \DateTime();
        $timeFormat = $this->timeFormat ?? '';
        $dateFormat = $this->dateFormat ?? '';
        return $datetime->format($dateFormat . $timeFormat) == false ? false : true;
    }

    public function getDateTimeFormat(): ?string
    {
        $timeFormat = $this->timeFormat ?? '';
        $dateFormat = $this->dateFormat ?? '';
        return empty($timeFormat) || empty($dateFormat) ? null : $dateFormat . $timeFormat;
    }
}
