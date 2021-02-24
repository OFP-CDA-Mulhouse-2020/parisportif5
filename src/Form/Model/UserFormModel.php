<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\User;
use App\Validator\UniqueUser;
use App\Form\Handler\AccountDocumentFormHandler;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

final class UserFormModel
{
    private ?int $userId = null;

    /**
     * @UniqueUser(
     *    message="Inscription impossible avec cette adresse email ! Veuillez en donner une autre pour vous inscrire.",
     *    groups={"registration"}
     * )
     * @Assert\NotBlank(
     *     message="L'adresse email ne peut pas être vide.",
     *     normalizer="trim",
     *     groups={"registration"}
     * )
     * @Assert\Email(
     *     message="L'adresse email indiqué n'est pas valide.",
     *     mode="html5",
     *     groups={"registration"}
     * )
     */
    private ?string $email = null;

    /**
     * @UniqueUser(
     *    message="Modification impossible avec cette nouvelle adresse email ! Veuillez en donner une autre pour vous la modifier.",
     *    groups={"identifier_update"}
     * )
     * @Assert\NotBlank(
     *     message="La nouvelle adresse email ne peut pas être vide.",
     *     normalizer="trim",
     *     groups={"identifier_update"}
     * )
     * @Assert\Email(
     *     message="La nouvelle adresse email indiqué n'est pas valide.",
     *     mode="html5",
     *     groups={"identifier_update"}
     * )
     * @Assert\NotIdenticalTo(
     *     message="La nouvelle adresse email est identique à la précédente.",
     *     propertyPath="email",
     *     groups={"identifier_update"}
     * )
     */
    private ?string $newEmail = null;

    /**
     * @Assert\NotBlank(
     *     message="Le mot de passe ne peut pas être vide.",
     *     normalizer="trim",
     *     groups={"registration", "password_update"}
     * )
     * @Assert\Length(
     *     min=7,
     *     max=4096,
     *     minMessage="Votre mot de passe doit avoir au moins {{ limit }} caractères alphanumérique et/ou spéciaux.",
     *     maxMessage="Longueur maximale autorisée par Symfony pour des raisons de sécurité.",
     *     groups={"registration", "password_update"}
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}]+$/u",
     *     match=false,
     *     message="Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des lettres.",
     *     groups={"registration", "password_update"}
     * )
     * @Assert\Regex(
     *     pattern="/^\d+$/",
     *     match=false,
     *     message="Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des chiffres.",
     *     groups={"registration", "password_update"}
     * )
     * @Assert\NotCompromisedPassword(
     *     message="Mot de passe déclaré comme compromis.",
     *     groups={"registration", "password_update"}
     * )
     */
    private ?string $newPassword = null;

    /**
     * @Assert\NotBlank(
     *     message="Le mot de passe ne peut pas être vide.",
     *     normalizer="trim",
     *     groups={"identifier_update", "password_update"}
     * )
     * @SecurityAssert\UserPassword(
     *     message="Ce n'est pas votre mot de passe actuel.",
     *     groups={"identifier_update", "password_update"}
     * )
     */
    private ?string $password = null;

    /**
     * @Assert\NotBlank(
     *     message="La civilité ne peut pas être vide.",
     *     normalizer="trim",
     *     allowNull=true,
     *     groups={"profile"}
     * )
     * @Assert\Length(
     *      max = 60,
     *      maxMessage = "La civilité ne peut pas être plus longue que {{ limit }} caractères.",
     *      groups={"profile"}
     * )
     */
    private ?string $civility = null;

    /**
     * @Assert\NotBlank(
     *     message="Le prénom ne peut pas être vide.",
     *     normalizer="trim",
     *     groups={"registration", "profile"}
     * )
     * @Assert\Length(
     *     min=2,
     *     max=25,
     *     minMessage="Votre prénom doit avoir au moins {{ limit }} caractères.",
     *     maxMessage="Votre prénom ne doit pas avoir plus de {{ limit }} caractères.",
     *     groups={"registration", "profile"}
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\'\s]+$/u",
     *     message="Seules les lettres, les tirets et les apostrophes sont autorisés.",
     *     groups={"registration", "profile"}
     * )
     */
    private ?string $firstName = null;

    /**
     * @Assert\NotBlank(
     *     message="Le nom de famille ne peut pas être vide.",
     *     normalizer="trim",
     *     groups={"registration", "profile"}
     * )
     * @Assert\Length(
     *     min=2,
     *     max=25,
     *     minMessage="Votre nom de famille doit avoir au moins {{ limit }} caractères.",
     *     maxMessage="Votre nom de famille ne doit pas avoir plus de {{ limit }} caractères.",
     *     groups={"registration", "profile"}
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\'\s]+$/u",
     *     message="Seules les lettres, les tirets et les apostrophes sont autorisés.",
     *     groups={"registration", "profile"}
     * )
     */
    private ?string $lastName = null;

    /**
     * @Assert\NotBlank(
     *     message="L'adresse ne peut pas être vide.",
     *     normalizer="trim",
     *     groups={"registration", "profile"}
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\'\s\d\,]+$/u",
     *     message="Les caractères spéciaux ne sont pas autorisés pour l'adresse.",
     *     groups={"registration", "profile"}
     * )
     */
    private ?string $billingAddress = null;

    /**
     * @Assert\NotBlank(
     *     message="La ville ne peut pas être vide.",
     *     normalizer="trim",
     *     groups={"registration", "profile"}
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\'\s]+$/u",
     *     message="Les chiffres et les caractères spéciaux ne sont pas autorisés pour la ville.",
     *     groups={"registration", "profile"}
     * )
     */
    private ?string $billingCity = null;

    /**
     * @Assert\NotBlank(
     *     message="Le code postal ne peut pas être vide.",
     *     normalizer="trim",
     *     groups={"registration", "profile"}
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\s\d]+$/u",
     *     message="Les caractères spéciaux ne sont pas autorisés pour le code postal.",
     *     groups={"registration", "profile"}
     * )
     */
    private ?string $billingPostcode = null;

    /**
     * @Assert\NotBlank(
     *     message="Le pays ne peut pas être vide.",
     *     normalizer="trim",
     *     groups={"registration", "profile"}
     * )
     * @Assert\Country(
     *     message="Le pays indiqué n'est pas valide.",
     *     groups={"registration", "profile"}
     * )
     */
    private ?string $billingCountry = null;

    /**
     * @Assert\NotBlank(
     *     message="La date de naissance ne peut pas être vide.",
     *     normalizer="trim",
     *     groups={"registration"}
     * )
     * @Assert\LessThanOrEqual(
     *     value="-18 year UTC",
     *     message="Vous n'avez pas l'âge requis de 18 ans pour vous inscrire.",
     *     groups={"registration"}
     * )
     * @Assert\GreaterThanOrEqual(
     *     value="-140 year UTC",
     *     message="Vous dépassez l'âge maximum de 140 ans pour vous inscrire.",
     *     groups={"registration"}
     * )
     */
    private ?\DateTimeImmutable $birthDate = null;

    /**
     * @Assert\NotBlank(
     *     message="Le fuseau horaire sélectionné ne peut pas être vide.",
     *     normalizer="trim",
     *     groups={"registration", "parameter"}
     * )
     * @Assert\Timezone(
     *     message="Le fuseau horaire sélectionné {{ value }} n'est pas valide.",
     *     groups={"registration", "parameter"}
     * )
     */
    private ?string $timeZoneSelected = null;

    private bool $deletedStatus = false;

    private ?\DateTimeImmutable $deletedDate = null;

    private bool $suspendedStatus = false;

    private ?\DateTimeImmutable $suspendedDate = null;

    private bool $activatedStatus = false;

    private ?\DateTimeImmutable $activatedDate = null;

    private bool $isVerified = false;

    private bool $newsletters = false;

    /**
     * @Assert\NotBlank(
     *     message="Le nom du fichier justifiant l'identité ne peut pas être vide.",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/\.(pdf|png|jpeg|jpg)$/i",
     *     message="Seule les fichiers au format PDF, PNG, JPG et JPEG sont accepté"
     * )
     */
    private ?string $identityDocumentFileName = null;

    /**
     * @Assert\NotBlank(
     *     message="Le nom du fichier justifiant le domicile ne peut pas être vide.",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/\.(pdf|png|jpeg|jpg)$/i",
     *     message="Seule les fichiers au format PDF, PNG, JPG et JPEG sont accepté"
     * )
     */
    private ?string $residenceProofFileName  = null;

    /**
     * @Assert\NotBlank(
     *     message="Fichier obligatoire !",
     *     normalizer="trim",
     *     groups={"registration", "identity_document"}
     * )
     * @Assert\File(
     *     maxSize = "1Mi",
     *     mimeTypes = {"application/pdf", "application/x-pdf", "image/jpeg", "image/png"},
     *     mimeTypesMessage = "Seule les fichiers au format PDF, PNG, JPG et JPEG sont accepté.",
     *     disallowEmptyMessage = "Le fichier spécifier est vide.",
     *     maxSizeMessage = "Le fichier est trop volumineux. La taille maximale autorisée est de 1 Mio.",
     *     groups={"registration", "identity_document"}
     * )
     */
    private ?UploadedFile $identityDocument = null;

    /**
     * @Assert\NotBlank(
     *     message="Fichier obligatoire !",
     *     normalizer="trim",
     *     groups={"registration", "residence_document"}
     * )
     * @Assert\File(
     *     maxSize = "1Mi",
     *     mimeTypes = {"application/pdf", "application/x-pdf", "image/jpeg", "image/png"},
     *     mimeTypesMessage = "Seule les fichiers au format PDF, PNG, JPG et JPEG sont accepté.",
     *     disallowEmptyMessage = "Le fichier spécifier est vide.",
     *     maxSizeMessage = "Le fichier est trop volumineux. La taille maximale autorisée est de 1 Mio.",
     *     groups={"registration", "residence_document"}
     * )
     */
    private ?UploadedFile $residenceProof = null;

    /**
     * @Assert\IsTrue(
     *     message="Vous devez accepter les conditions générales d'utilisation pour vous inscrire.",
     *     groups={"registration"}
     * )
     */
    private bool $acceptTerms = false;

    /**
     * @Assert\IsTrue(
     *     message="Vous devez certifier sur l'honneur que les données fournies sont exactes.",
     *     groups={"registration", "identity_document", "residence_document"}
     * )
     */
    private bool $certifiesAccurate = false;

    public static function createFromUser(
        User $user
    ): UserFormModel {
        $dto = new static();

        $dto->userId = $user->getId();
        $dto->email = $user->getEmail();
        $dto->civility = $user->getCivility();
        $dto->firstName = $user->getFirstName();
        $dto->lastName = $user->getLastName();
        $dto->billingAddress = $user->getBillingAddress();
        $dto->billingCity = $user->getBillingCity();
        $dto->billingPostcode = $user->getBillingPostcode();
        $dto->billingCountry = $user->getBillingCountry();
        $dto->birthDate = $user->getBirthDate();
        $dto->timeZoneSelected = $user->getTimeZoneSelected();
        $dto->deletedDate = $user->getDeletedDate();
        $dto->deletedStatus = $user->getDeletedStatus();
        $dto->suspendedDate = $user->getSuspendedDate();
        $dto->suspendedStatus = $user->getSuspendedStatus();
        $dto->activatedDate = $user->getActivatedDate();
        $dto->activatedStatus = $user->getActivatedStatus();
        $dto->isVerified = $user->isVerified();
        $dto->newsletters = $user->getNewsletters();
        //$dto->identityDocumentFileName = $user->getIdentityDocument();
        //$dto->residenceProofFileName = $user->getResidenceProof();
        $dto->identityDocumentFileName =
            AccountDocumentFormHandler::getBasenameFromFormated(
                $user->getIdentityDocument()
            )
        ;
        $dto->residenceProofFileName =
            AccountDocumentFormHandler::getBasenameFromFormated(
                $user->getResidenceProof()
            )
        ;
        $dto->acceptTerms = false;
        $dto->certifiesAccurate = false;

        return $dto;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        $this->isVerified = false;
        return $this;
    }

    public function getNewEmail(): ?string
    {
        return $this->newEmail;
    }

    public function setNewEmail(?string $newEmail): self
    {
        $this->newEmail = $newEmail;
        $this->isVerified = false;
        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(?string $newPassword): self
    {
        $this->newPassword = $newPassword;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function erasePasswords(): self
    {
        $this->password = null;
        $this->newPassword = null;
        return $this;
    }

    /**
     * @Assert\IsTrue(
     *     message="Le mot de passe ne doit pas contenir le prénom et/ou le nom.",
     *     groups={"password_update", "registration"}
     * )
     */
    public function isNewPasswordSafe(): bool
    {
        return (stripos($this->newPassword ?? '', $this->lastName ?? '') === false
            && stripos($this->newPassword ?? '', $this->firstName ?? '') === false);
    }

    public function getCivility(): ?string
    {
        return $this->civility;
    }

    public function setCivility(?string $civility): self
    {
        $this->civility = $civility;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?string $billingAddress): self
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }

    public function getBillingCity(): ?string
    {
        return $this->billingCity;
    }

    public function setBillingCity(?string $billingCity): self
    {
        $this->billingCity = $billingCity;
        return $this;
    }

    public function getBillingPostcode(): ?string
    {
        return $this->billingPostcode;
    }

    public function setBillingPostcode(?string $billingPostcode): self
    {
        $this->billingPostcode = $billingPostcode;
        return $this;
    }

    public function getBillingCountry(): ?string
    {
        return $this->billingCountry;
    }

    public function setBillingCountry(?string $billingCountry): self
    {
        $this->billingCountry = $billingCountry;
        return $this;
    }

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeImmutable $birthDate): self
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getTimeZoneSelected(): ?string
    {
        return $this->timeZoneSelected;
    }

    public function setTimeZoneSelected(?string $timeZoneSelected): self
    {
        $this->timeZoneSelected = $timeZoneSelected;
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

    public function getSuspendedStatus(): ?bool
    {
        return $this->suspendedStatus;
    }

    public function getSuspendedDate(): ?\DateTimeImmutable
    {
        return $this->suspendedDate;
    }

    public function getActivatedStatus(): ?bool
    {
        return $this->activatedStatus;
    }

    public function getActivatedDate(): ?\DateTimeImmutable
    {
        return $this->activatedDate;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getNewsletters(): bool
    {
        return $this->newsletters;
    }

    public function setNewsletters(bool $newsletters): self
    {
        $this->newsletters = $newsletters;
        return $this;
    }

    public function getIdentityDocument(): ?UploadedFile
    {
        return $this->identityDocument;
    }

    public function setIdentityDocument(?UploadedFile $identityDocument): self
    {
        $this->identityDocument = $identityDocument;
        return $this;
    }

    public function getResidenceProof(): ?UploadedFile
    {
        return $this->residenceProof;
    }

    public function setResidenceProof(?UploadedFile $residenceProof): self
    {
        $this->residenceProof = $residenceProof;
        return $this;
    }

    public function getIdentityDocumentFileName(): ?string
    {
        return $this->identityDocumentFileName;
    }

    public function setIdentityDocumentFileName(?string $identityDocumentFileName): self
    {
        $this->identityDocumentFileName = $identityDocumentFileName;
        return $this;
    }

    public function getResidenceProofFileName(): ?string
    {
        return $this->residenceProofFileName;
    }

    public function setResidenceProofFileName(?string $residenceProofFileName): self
    {
        $this->residenceProofFileName = $residenceProofFileName;
        return $this;
    }

    public function setAcceptTerms(bool $acceptTerms): self
    {
        $this->acceptTerms = $acceptTerms;
        return $this;
    }

    public function getAcceptTerms(): bool
    {
        return $this->acceptTerms;
    }

    public function setCertifiesAccurate(bool $certifiesAccurate): self
    {
        $this->certifiesAccurate = $certifiesAccurate;
        return $this;
    }

    public function getCertifiesAccurate(): bool
    {
        return $this->certifiesAccurate;
    }
}
