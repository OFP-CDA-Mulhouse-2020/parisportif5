<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\LanguageRepository;

/**
 * @ORM\Entity(repositoryClass=LanguageRepository::class)
 * @UniqueEntity(
 *     fields="code",
 *     message="Cette langue est déjà enregistré."
 * )
 */
class Language extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=2)
     * @Assert\NotBlank(
     *     message="Le nom du langage ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Language(
     *     message="Le langage {{ value }} n'est pas valide",
     * )
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=2)
     * @Assert\NotBlank(
     *     message="Le pays du langage ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Country(
     *     message="Le pays du langage {{ value }} n'est pas valide",
     * )
     */
    private string $country;

    /**
     * @ORM\Column(type="string", length=5)
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
     * "/^(\\T)?[HGhg](\s?(\:|(\\\p{L})+)\s?)i((\s?(\:|[Aa]|(\\\p{L})+)\s?)(s(\.u|\.vP?|P|O|\sO|\s?[Aa]|\s?(\\\p{L})+)?)?)?(\s?T)?$/u"
     * )
     */
    private string $timeFormat;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank(
     *     message="Le fuseau horaire ne peut pas être vide.",
     *     normalizer="trim"
     * )
     * @Assert\Timezone(
     *     message="Le fuseau horaire {{ value }} n'est pas valide."
     * )
     */
    private string $capitalTimeZone;

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

    public function getCapitalTimeZone(): ?string
    {
        return $this->capitalTimeZone;
    }

    public function setCapitalTimeZone(string $capitalTimeZone): self
    {
        $this->capitalTimeZone = $capitalTimeZone;
        return $this;
    }

    public function getDateTimeFormat(): ?string
    {
        $timeFormat = $this->timeFormat ?? '';
        $dateFormat = $this->dateFormat ?? '';
        return empty($timeFormat) || empty($dateFormat) ? null : $dateFormat . $timeFormat;
    }

    public function __toString(): string
    {
        return $this->id . ' - ' . $this->name;
    }
}
