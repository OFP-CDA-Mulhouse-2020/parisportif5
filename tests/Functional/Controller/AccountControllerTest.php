<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

// paramètre test

/**
 * @covers \AccountController
 */
final class AccountControllerTest extends WebTestCase
{
    /*private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('tintin.dupont@test.fr');

        // simulate $testUser being logged in
        $this->client->loginUser($testUser);
    }*/

    protected function loginTestUser(): KernelBrowser
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('tintin.dupont@test.fr');
        // simulate $testUser being logged in
        $client->loginUser($testUser);
        return $client;
    }

    protected function getPersonalDataPageUrl(): string
    {
        return '/mon-compte/mes-informations';
    }

    protected function getDocumentPageUrl(): string
    {
        return '/mon-compte/mes-documents';
    }

    protected function getParameterPageUrl(): string
    {
        return '/mon-compte/mes-parametres';
    }

    protected function getPasswordPageUrl(): string
    {
        return '/mon-compte/modifier/mot-de-passe';
    }

    protected function getIdentifierPageUrl(): string
    {
        return '/mon-compte/modifier/identifiant';
    }

    protected function getUpdateUserData(): array
    {
        return [
            'civility' => 'Madame',
            'firstName' => "Tata",
            'lastName' => "Dupond",
            'address' => "2 avenue st martin",
            'city' => "Strasbourg",
            'postcode' => "67000",
            'country' => "FR",
            'birthDate' => "2000-10-20",
            'newPlainPassword1' => "Azerty80",
            'newPlainPassword2' => "Azerty80",
            'oldPlainPassword' => "@Hadock5",
            'newEmail1' => "tata.dupond@test.fr",
            'newEmail2' => "tata.dupond@test.fr",
            'timezone' => "Europe/Paris",
            'residence' => __DIR__ . '/../../../docs/Wireframe/WireframeParisSportifs.pdf',
            'identity' => __DIR__ . '/../../../docs/Wireframe/WireframeParisSportifs.pdf',
            'accurate' => true,
            'newsletters' => true
        ];
    }

    // Tests fonctionnels d'intégrations

    /**
     * @dataProvider accountFormValidPageProvider
     */
    public function testIfPageIsRedirectWithoutUser(string $url): void
    {
        $client = static::createClient();

        $client->request('GET', $url);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/connexion');
    }

    /**
     * @dataProvider accountFormValidPageProvider
     */
    public function testAccountFormValidPage(string $url, string $title): void
    {
        $client = $this->loginTestUser();
        $client->request('GET', $url);
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', $title);
    }

    public function accountFormValidPageProvider(): array
    {
        return [
            [$this->getPersonalDataPageUrl(), "Données personnelles"],
            [$this->getDocumentPageUrl(), "Vos documents"],
            [$this->getParameterPageUrl(), "Vos paramètres"],
            [$this->getPasswordPageUrl(), "Modifier le mot de passe du compte"],
            [$this->getIdentifierPageUrl(), "Modifier l'identifiant du compte"]
        ];
    }

    public function testAccountPersonalDataFormValidDisplay(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formName = $crawler->filter('form *[name*=modifyUserProfile][type=submit]')->form()->getName();
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . ']'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Civilité
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[name*=civility]'),
            "Il doit y avoir un et un seul champ pour la civilité dans ce formulaire"
        );
        // Email à titre indicatif
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=email][type=email]'),
            "Il doit y avoir un et un seul champ pour l'email à titre indicatif dans ce formulaire"
        );
        // Mot de passe vide à titre indicatif
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=zeroPassword][type=text]'),
            "Il doit y avoir un et un seul champ pour le mot de passe vide à titre indicatif dans ce formulaire"
        );
        // Nom
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=lastName]'),
            "Il doit y avoir un et un seul champ pour le nom dans ce formulaire"
        );
        // Prénom
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=firstName]'),
            "Il doit y avoir un et un seul champ pour le prénom dans ce formulaire"
        );
        // Adresse
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=Address]'),
            "Il doit y avoir un et un seul champ pour l'adresse dans ce formulaire"
        );
        // Ville
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=City]'),
            "Il doit y avoir un et un seul champ pour la ville dans ce formulaire"
        );
        // Code postal
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=Postcode]'),
            "Il doit y avoir un et un seul champ pour le code postal dans ce formulaire"
        );
        // Pays
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[name*=Country]'),
            "Il doit y avoir un et un seul champ pour le pays dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    protected function getAccountPersonalDataForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->filter('form *[name*=modifyUserProfile][type=submit]')->form();
        $formName = $form->getName();
        $form->disableValidation();
        $form[$formName . '[civility]'] = $formData['civility'];
        $form[$formName . '[lastName]'] = $formData['lastName'];
        $form[$formName . '[firstName]'] = $formData['firstName'];
        $form[$formName . '[billingAddress]'] = $formData['address'];
        $form[$formName . '[billingCity]'] = $formData['city'];
        $form[$formName . '[billingPostcode]'] = $formData['postcode'];
        $form[$formName . '[billingCountry]'] = $formData['country'];
        return $form;
    }

    public function testAccountPersonalDataFormCivilityInvalid(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $civility = 'inconnu';
        $formData['civility'] = $civility;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "La valeur n'est pas valide."
        );
    }

    public function testAccountPersonalDataFormFirstNameEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $firstName = ' ';
        $formData['firstName'] = $firstName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le prénom ne peut pas être vide."
        );
    }

    public function testAccountPersonalDataFormFirstNameNotValid(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $firstName = 'k2000';
        $formData['firstName'] = $firstName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Seules les lettres, les tirets et les apostrophes sont autorisés."
        );
    }

    public function testAccountPersonalDataFormFirstNameLengthUnderMin(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $firstName = 'a';
        $formData['firstName'] = $firstName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Votre prénom doit avoir au moins 2 caractères."
        );
    }

    public function testAccountPersonalDataFormFirstNameLengthOverMax(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $firstName = 'nomquiestbeaucouptroplongg';
        $formData['firstName'] = $firstName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Votre prénom ne doit pas avoir plus de 25 caractères."
        );
    }

    public function testAccountPersonalDataFormLastNameEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $lastName = ' ';
        $formData['lastName'] = $lastName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le nom de famille ne peut pas être vide."
        );
    }

    public function testAccountPersonalDataFormLastNameNotValid(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $lastName = 'k2000';
        $formData['lastName'] = $lastName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Seules les lettres, les tirets et les apostrophes sont autorisés."
        );
    }

    public function testAccountPersonalDataFormLastNameLengthUnderMin(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $lastName = 'a';
        $formData['lastName'] = $lastName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Votre nom de famille doit avoir au moins 2 caractères."
        );
    }

    public function testAccountPersonalDataFormLastNameLengthOverMax(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $lastName = 'nomquiestbeaucouptroplongg';
        $formData['lastName'] = $lastName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Votre nom de famille ne doit pas avoir plus de 25 caractères."
        );
    }

    public function testAccountPersonalDataFormAddressEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $address = ' ';
        $formData['address'] = $address;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "L'adresse ne peut pas être vide."
        );
    }

    public function testAccountPersonalDataFormAddressNotValid(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $address = '1 rue G@bin';
        $formData['address'] = $address;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Les caractères spéciaux ne sont pas autorisés pour l'adresse."
        );
    }

    public function testAccountPersonalDataFormCityEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $city = ' ';
        $formData['city'] = $city;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "La ville ne peut pas être vide."
        );
    }

    public function testAccountPersonalDataFormCityNotValid(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $city = 'St-Martin 1';
        $formData['city'] = $city;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Les chiffres et les caractères spéciaux ne sont pas autorisés pour la ville."
        );
    }

    public function testAccountPersonalDataFormPostcodeEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $postcode = ' ';
        $formData['postcode'] = $postcode;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le code postal ne peut pas être vide."
        );
    }

    public function testAccountPersonalDataFormPostcodeNotValid(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPersonalDataPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $postcode = '6800@';
        $formData['postcode'] = $postcode;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Les caractères spéciaux ne sont pas autorisés pour le code postal."
        );
    }

    public function testAccountDocumentFormValidDisplay(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getDocumentPageUrl());
        $formName = $crawler->filter('form *[name*=userIdentityDocumentReplace][type=submit]')->form()->getName();
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . ']'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Certifie l'exactitude des informations
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=certifiesAccurate][type=checkbox]'),
            "Il doit y avoir un et un seul champ pour certifier l'exactitude des informations dans ce formulaire"
        );
        // Justificatif de domicile
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=residenceProof][type=file]'),
            "Il doit y avoir un et un seul champ pour le justificatif de domicile dans ce formulaire"
        );
        // Bouton d'ajout du justificatif de domicile
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] [name*=userResidenceProofReplace][type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi pour l'ajout du justificatif de domicile dans ce formulaire"
        );
        // Justificatif d'identité
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=identityDocument][type=file]'),
            "Il doit y avoir un et un seul champ pour le justificatif d'identité dans ce formulaire"
        );
        // Bouton d'ajout du justificatif d'identité
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[name*=userIdentityDocumentReplace][type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi pour l'ajout du justificatif d'identité dans ce formulaire"
        );
    }

    protected function getAccountDocumentForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->filter('form *[name*=userIdentityDocumentReplace][type=submit]')->form();
        $formName = $form->getName();
        $form[$formName . '[certifiesAccurate]'] = $formData['accurate'];
        $form[$formName . '[residenceProof]'] = $formData['residence'];
        $form[$formName . '[identityDocument]'] = $formData['identity'];
        return $form;
    }

    public function testAccountDocumentFormCertifiesAccurateNotCheck(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getDocumentPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $certifiesAccurate = false;
        $formData['accurate'] = $certifiesAccurate;
        $form = $this->getAccountDocumentForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Vous devez certifier sur l'honneur que les données fournies sont exactes"
        );
    }

    public function testAccountDocumentFormIdentityDocumentEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getDocumentPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $identityDocument = '';
        $formData['identity'] = $identityDocument;
        $form = $this->getAccountDocumentForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Fichier obligatoire !"
        );
    }

    public function testAccountDocumentFormIdentityDocumentSizeNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getDocumentPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $identityDocument = __DIR__ . '/../../../docs/Wireframe/WireframeParisSportifs.zip';
        $formData['identity'] = $identityDocument;
        $form = $this->getAccountDocumentForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le fichier est trop volumineux. La taille maximale autorisée est de 1 Mio."
        );
    }

    public function testAccountDocumentFormIdentityDocumentFormatNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getDocumentPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $identityDocument = __DIR__ . '/../../../docs/Wireframe/WireframeParisSportifs.bmpr';
        $formData['identity'] = $identityDocument;
        $form = $this->getAccountDocumentForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Seule les fichiers au format PDF, PNG, JPG et JPEG sont accepté."
        );
    }

    public function testAccountDocumentFormResidenceProofEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getDocumentPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $residenceProof = '';
        $formData['residence'] = $residenceProof;
        $form = $this->getAccountDocumentForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Fichier obligatoire !"
        );
    }

    public function testAccountDocumentFormResidenceProofSizeNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getDocumentPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $residenceProof = __DIR__ . '/../../../docs/Wireframe/WireframeParisSportifs.zip';
        $formData['residence'] = $residenceProof;
        $form = $this->getAccountDocumentForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le fichier est trop volumineux. La taille maximale autorisée est de 1 Mio."
        );
    }

    public function testAccountDocumentFormResidenceProofFormatNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getDocumentPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $residenceProof = __DIR__ . '/../../../docs/Wireframe/WireframeParisSportifs.bmpr';
        $formData['residence'] = $residenceProof;
        $form = $this->getAccountDocumentForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Seule les fichiers au format PDF, PNG, JPG et JPEG sont accepté."
        );
    }

    public function testAccountIdentifierFormValidDisplay(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getIdentifierPageUrl());
        $formName = $crawler->filter('form *[name*=modifyUserIdentifier][type=submit]')->form()->getName();
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . ']'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Mot de passe actuel
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[id$=password][type=password]'),
            "Il doit y avoir un et un seul champ pour le mot de passe actuel dans ce formulaire"
        );
        // Nouveau email
        $this->assertCount(
            2,
            $crawler->filter(
                'form[name=' . $formName . '] input[id^=' . $formName . '_newEmail_][type=email]'
            ),
            "Il doit y avoir 2 et seulement 2 champs pour la nouvelle adresse email dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    protected function getAccountIdentifierForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->filter('form *[name*=modifyUserIdentifier][type=submit]')->form();
        $formName = $form->getName();
        $form[$formName . '[password]'] = $formData['oldPlainPassword'];
        $form[$formName . '[newEmail][first]'] = $formData['newEmail1'];
        $form[$formName . '[newEmail][second]'] = $formData['newEmail2'];
        return $form;
    }

    public function testAccountIdentifierFormFalsePassword(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getIdentifierPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $passwordOld = 'a9c456';
        $formData['oldPlainPassword'] = $passwordOld;
        $form = $this->getAccountIdentifierForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Ce n'est pas votre mot de passe actuel."
        );
    }

    public function testAccountIdentifierFormEmailEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getIdentifierPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $email = ' ';
        $formData['newEmail1'] = $email;
        $formData['newEmail2'] = $email;
        $form = $this->getAccountIdentifierForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "La nouvelle adresse email ne peut pas être vide."
        );
    }

    public function testAccountIdentifierFormEmailNotEqual(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getIdentifierPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $email = 'dupond@orange.fr';
        $formData['newEmail2'] = $email;
        $form = $this->getAccountIdentifierForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Veuillez saisir votre nouvelle adresse email de façon identique aux deux endroits spécifiés."
        );
    }

    public function testAccountIdentifierFormEmailNotValid(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getIdentifierPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $email = 'test';
        $formData['newEmail1'] = $email;
        $formData['newEmail2'] = $email;
        $form = $this->getAccountIdentifierForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "La nouvelle adresse email indiqué n'est pas valide."
        );
    }

    public function testAccountPasswordFormValidDisplay(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPasswordPageUrl());
        $formName = $crawler->filter('form *[name*=modifyUserPassword][type=submit]')->form()->getName();
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . ']'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Ancien mot de passe
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[id$=password][type=password]'),
            "Il doit y avoir un et un seul champ pour l'ancien mot de passe dans ce formulaire"
        );
        // Nouveau mot de passe
        $this->assertCount(
            2,
            $crawler->filter(
                'form[name=' . $formName . '] input[id^=' . $formName . '_newPassword_][type=password]'
            ),
            "Il doit y avoir 2 et seulement 2 champs pour le nouveau mot de passe dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    protected function getAccountPasswordForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->filter('form *[name*=modifyUserPassword][type=submit]')->form();
        $formName = $form->getName();
        $form[$formName . '[password]'] = $formData['oldPlainPassword'];
        $form[$formName . '[newPassword][first]'] = $formData['newPlainPassword1'];
        $form[$formName . '[newPassword][second]'] = $formData['newPlainPassword2'];
        return $form;
    }

    public function testAccountPasswordFormPasswordWithName(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPasswordPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $password = 'Tintin_Dupont';
        $formData['newPlainPassword1'] = $password;
        $formData['newPlainPassword2'] = $password;
        $form = $this->getAccountPasswordForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le mot de passe ne doit pas contenir le prénom et/ou le nom."
        );
    }

    public function testAccountPasswordFormFalseOldPassword(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPasswordPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $passwordOld = 'a9c456';
        $formData['oldPlainPassword'] = $passwordOld;
        $form = $this->getAccountPasswordForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Ce n'est pas votre mot de passe actuel."
        );
    }

    public function testAccountPasswordFormPasswordUnderMin(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPasswordPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $password = 'a2c456';
        $formData['newPlainPassword1'] = $password;
        $formData['newPlainPassword2'] = $password;
        $form = $this->getAccountPasswordForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Votre mot de passe doit avoir au moins 7 caractères alphanumérique et/ou spéciaux."
        );
    }

    public function testAccountPasswordFormPasswordOnlyNumbers(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPasswordPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $password = '12345678';
        $formData['newPlainPassword1'] = $password;
        $formData['newPlainPassword2'] = $password;
        $form = $this->getAccountPasswordForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des chiffres."
        );
    }

    public function testAccountPasswordFormPasswordOnlyLetters(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPasswordPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $password = 'azertyuiop';
        $formData['newPlainPassword1'] = $password;
        $formData['newPlainPassword2'] = $password;
        $form = $this->getAccountPasswordForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des lettres."
        );
    }

    public function testAccountPasswordFormPasswordNotEqual(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPasswordPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $password = 'Azerty789';
        $formData['newPlainPassword2'] = $password;
        $form = $this->getAccountPasswordForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Veuillez saisir un nouveau mot de passe valide."
        );
    }

    public function testAccountPasswordFormPasswordEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getPasswordPageUrl());
        $formData = $this->getUpdateUserData();
        // set some values
        $password = ' ';
        $formData['newPlainPassword1'] = $password;
        $formData['newPlainPassword2'] = $password;
        $form = $this->getAccountPasswordForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le mot de passe ne peut pas être vide."
        );
    }

    public function testAccountParameterFormValidDisplay(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getParameterPageUrl());
        $formName = $crawler->filter('form *[name*=modifyUserParameters][type=submit]')->form()->getName();
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . ']'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Fuseau horaire
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[name*=timeZoneSelected]'),
            "Il doit y avoir un et un seul champ pour le fuseau horaire dans ce formulaire"
        );
        // Abonnement d'offre et de publicité (Newsletters)
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[name*=newsletters][type=checkbox]'),
            "Il doit y avoir un et un seul champ pour accepter la newsletters dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    protected function getAccountParameterForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->filter('form *[name*=modifyUserParameters][type=submit]')->form();
        $formName = $form->getName();
        $form[$formName . '[timeZoneSelected]'] = $formData['timezone'];
        $form[$formName . '[newsletters]'] = $formData['newsletters'];
        return $form;
    }
}
