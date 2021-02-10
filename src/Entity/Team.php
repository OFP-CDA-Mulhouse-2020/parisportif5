<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TeamRepository;

/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 * @UniqueEntity(
 *     fields={"name", "country", "sport"},
 *     errorPath="name",
 *     message="Cette équipe est déjà enregistrée."
 * )
 */
class Team extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     normalizer="trim",
     *     message="Le nom de l'équipe ne peut pas être vide",
     * )
     * @Assert\Regex(
     *     pattern="/^\p{L}((?<!\')\'|\p{L}?\s?(?!\s)(\.(?!\.))?|\-(?!\-)|\s(?!\s)|\d)+\S$/u",
     *     message="Seules les chiffres, les lettres, les tirets, les apostrophes et les points sont autorisés"
     * )
     * @Assert\Length(
     *     min=2,
     *     minMessage="Le nom de l'équipe doit avoir au moins {{ limit }} caractères",
     * )
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le pays doit être renseigné",
     *     normalizer="trim"
     * )
     * @Assert\Country(
     *     message="Le pays {{ value }} n'est pas valide",
     * )
     */
    private string $country;

    /**
     * @var Collection<int,Member> $members
     * @ORM\OneToMany(targetEntity=Member::class, mappedBy="team")
     * @Assert\Valid
     */
    private Collection $members;

    /**
     * @ORM\ManyToOne(targetEntity=Sport::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private Sport $sport;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\Type(
     *     type="numeric",
     *     message="La côte de l'équipe doit être un nombre entier ou réel."
     * )
     * @Assert\PositiveOrZero(
     *     message="La côte de l'équipe doit être un positif ou zéro."
     * )
     * @Assert\LessThan(
     *     value=100000000,
     *     message="La côte de l'équipe doit être inférieur à {{ compared_value }}."
     * )
     */
    private string $odds;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

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

    /**
     * @return Collection<int,Member>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Member $member): self
    {
        if (!$this->members->contains($member)) {
            if (empty($this->sport)) {
                return $this;
            }
            $maxMembers = $this->sport->getMaxMembersByTeam() ?? 0;
            if ($maxMembers > 0 && count($this->members) >= $maxMembers) {
                return $this;
            }
            $this->members[] = $member;
            $member->setTeam($this);
        }
        return $this;
    }

    public function removeMember(Member $member): self
    {
        $this->members->removeElement($member);
        return $this;
    }

    public function getSport(): ?Sport
    {
        return $this->sport;
    }

    public function setSport(Sport $sport): self
    {
        $this->sport = $sport;
        return $this;
    }

    /**
     * @Assert\IsTrue(
     *     message="Le nombre requis de membre n'est pas atteint ou est dépassé"
     * )
     */
    public function hasRequiredNumberOfMembers(): bool
    {
        $membersCount = $this->getMembers()->count();
        $minMembers = 0;
        if (!empty($this->sport)) {
            $minMembers = $this->sport->getMinMembersByTeam();
        }
        $maxMembers = 0;
        if (!empty($this->sport)) {
            $maxMembers = $this->sport->getMaxMembersByTeam() ?? $minMembers;
        }
        return ($minMembers === 0 && $maxMembers === 0) ?:
            ($minMembers <= $membersCount && $maxMembers >= $membersCount);
    }

    public function getOdds(): ?string
    {
        return $this->odds;
    }

    public function setOdds(string $odds): self
    {
        $this->odds = $odds;
        return $this;
    }

    public function hasMembers(): bool
    {
        return !$this->members->isEmpty();
    }

    public function getMembersCount(): int
    {
        return $this->members->count();
    }

    public function __toString(): string
    {
        return $this->id . ' - ' . $this->name . ' (' . $this->country . ')';
    }
}
