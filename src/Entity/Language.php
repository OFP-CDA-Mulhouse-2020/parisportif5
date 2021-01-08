<?php

declare(strict_types=1);

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
     *     canonicalize=true,
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
     *  pattern=
     *  "/^([Dl]\,\s)?((([dj](\-|\s|\/)[mn]|[mn](\-|\s|\/)[dj])(\-|\s|\/)Y)|(Y(\-|\s|\/)[mn](\-|\s|\/)[dj]))\s?$/u",
     *  message="Certains caractères spéciaux et paramètres pour le format de la date ne sont pas autorisés"
     * )
     */
    private string $dateFormat;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(
     *     message="Le format de l'heure ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *  message="Certains caractères spéciaux et paramètres pour le format de l'heure ne sont pas autorisés",
     *  pattern=
     * "/^(\\T)?[HG](\s?(\:|(\\\p{L})+)\s?)i((\s?(\:|(\\\p{L})+)\s?)(s(\.u|\.vP?|P|O|\sO|\s?(\\\p{L})+)?)?)?(\s?T)?$/u"
     * )
     */
    private string $timeFormat;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le fuseau horaire ne peut pas être vide.",
     *     normalizer="trim"
     * )
     * @Assert\Timezone(
     *     message="Le fuseau horaire {{ value }} n'est pas valide."
     * )
     */
    private string $timeZone;

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

    public function getTimeZone(): ?string
    {
        return $this->timeZone;
    }

    public function setTimeZone(string $timeZone): self
    {
        $this->timeZone = $timeZone;
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
