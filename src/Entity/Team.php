<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\TeamRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 */
class Team
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
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^\p{L}((?<!\')\'|\p{L}?\s?(?!\s)(\.(?!\.))?|\-(?!\-)|\s(?!\s)|\d)+\S$/u",
     *     message="Seules les chiffres, les lettres et les caractères suivants (., -, ') sont autorisés"
     * )
     * @Assert\Length(
     *     min=2,
     *     minMessage="Le nom de l'équipe doit avoir plus de {{ limit }} caractères",
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
            $this->members[] = $member;
            $member->setTeam($this);
        }

        return $this;
    }

    public function removeMember(Member $member): self
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            // if ($member->getTeam() === $this) {
            //     $member->setTeam(null);
            // }
        }

        return $this;
    }
}
