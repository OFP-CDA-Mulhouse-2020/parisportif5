<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

/**
 * @covers \UserProfileController
 */
final class UserProfileControllerTest extends WebTestCase
{
    private function getValidUserData(): array
    {
        return [
            'civility' => '',
            'firstName' => "Martin",
            'lastName' => "Dupond",
            'address' => "1 avenue st martin",
            'city' => "Colmar",
            'postcode' => "68000",
            'country' => "FR",
            'birthDate' => "2000-10-10",
            'password1' => "Azerty78",
            'password2' => "Azerty78",
            'email1' => "dupond.m@orange.fr",
            'email2' => "dupond.m@orange.fr",
            'timezone' => "Europe/Paris",
            'residence' => 'filename.pdf',
            'identity' => 'filename.pdf',
            'accurate' => true,
            'newsletters' => false
        ];
    }

    // Tests fonctionnels d'intégrations

    /**
     * @dataProvider profileFormValidPageProvider
     */
    public function testProfileFormValidPage(string $url, string $title): void
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', $title);
    }

    public function profileFormValidPageProvider(): array
    {
        /*
            ['/mon-compte/mes-informations', "Données personnelles"],
            ['/mon-compte/mes-newsletters', "Vos abonnements"],
            ['/mon-compte/mes-documents', "Vos documents"],
            ['/mon-compte/mes-options', "Vos options"]
        */
        return [
            ['/mon-compte/mes-informations', "Données personnelles"],
            ['/mon-compte/mes-documents', "Vos documents"],
            ['/mon-compte/mes-parametres', "Vos paramètres"],
            ['/mon-compte/identifiants/modification', "Modification des identifiants de connexion au compte"]
        ];
    }

    public function testProfilePersonalDataFormValidDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Civilité
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] *[name*=civility]'),
            "Il doit y avoir un et un seul champ pour la civilité dans ce formulaire"
        );
        // Nom
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] input[name*=lastName]'),
            "Il doit y avoir un et un seul champ pour le nom dans ce formulaire"
        );
        // Prénom
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] input[name*=firstName]'),
            "Il doit y avoir un et un seul champ pour le prénom dans ce formulaire"
        );
        // Adresse
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] input[name*=Address]'),
            "Il doit y avoir un et un seul champ pour l'adresse dans ce formulaire"
        );
        // Ville
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] input[name*=City]'),
            "Il doit y avoir un et un seul champ pour la ville dans ce formulaire"
        );
        // Code postal
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] input[name*=Postcode]'),
            "Il doit y avoir un et un seul champ pour le code postal dans ce formulaire"
        );
        // Pays
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] *[name*=Country]'),
            "Il doit y avoir un et un seul champ pour le pays dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    public function getProfilePersonalDataForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('user_profile_personal_data[modify]')->form();
        $form['user_profile_personal_data[civility]'] = $formData['civility'];
        $form['user_profile_personal_data[lastName]'] = $formData['lastName'];
        $form['user_profile_personal_data[firstName]'] = $formData['firstName'];
        $form['user_profile_personal_data[billingAddress]'] = $formData['address'];
        $form['user_profile_personal_data[billingCity]'] = $formData['city'];
        $form['user_profile_personal_data[billingPostcode]'] = $formData['postcode'];
        $form['user_profile_personal_data[billingCountry]'] = $formData['country'];
        return $form;
    }

    public function testProfileDocumentFormValidDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-documents');
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_document]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Justificatif de domicile
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_document] *[name*=residenceProofFile]'),
            "Il doit y avoir un et un seul champ pour le justificatif de domicile dans ce formulaire"
        );
        // Bouton d'ajout du justificatif de domicile
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_document] *[name*=residenceProofAdd]'),
            "Il doit y avoir un et un seul champ pour le bouton d'ajout du justificatif de domicile dans ce formulaire"
        );
        // Justificatif d'identité
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_document] *[name*=identityDocumentFile]'),
            "Il doit y avoir un et un seul champ pour le justificatif d'identité dans ce formulaire"
        );
        // Bouton d'ajout du justificatif d'identité
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_document] *[name*=identityDocumentAdd]'),
            "Il doit y avoir un et un seul champ pour le bouton d'ajout du justificatif d'identité dans ce formulaire"
        );
        // Bouton total de submit
        $this->assertCount(
            2,
            $crawler->filter('form[name=user_profile_document] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    /*public function getProfileDocumentForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('user_profile_document[modify]')->form();
        $form['user_profile_document[residenceProof]'] = $formData['residence'];
        $form['user_profile_document[identityDocument]'] = $formData['identity'];
        return $form;
    }*/

    public function testProfileIdentifierFormValidDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/identifiants/modification');
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_identifier]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Mot de passe
        $this->assertSelectorExists(
            'form[name=user_profile_identifier] input[name*=password]',
            "Aucun champs de mot de passe n'est présent dans ce formulaire"
        );
        $this->assertCount(
            2,
            $crawler->filter('form[name=user_profile_identifier] input[name*=password]'),
            "Il existe plus de 2 champs de mot de passe dans ce formulaire"
        );
        // Email
        $this->assertSelectorExists(
            'form[name=user_profile_identifier] input[name*=email]',
            "Aucun champs email n'est présent dans ce formulaire"
        );
        $this->assertCount(
            2,
            $crawler->filter('form[name=user_profile_identifier] input[name*=email]'),
            "Il existe plus de 2 champs email dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_identifier] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    public function getProfileIdentifierForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('user_profile_identifier[modify]')->form();
        $form['user_profile_identifier[password][first]'] = $formData['password1'];
        $form['user_profile_identifier[password][second]'] = $formData['password2'];
        $form['user_profile_identifier[email][first]'] = $formData['email1'];
        $form['user_profile_identifier[email][second]'] = $formData['email2'];
        return $form;
    }

    /*public function testProfileIdentifierFormPasswordUnderMin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/identifiants/modification');
        $formData = $this->getValidUserData();
        // set some values
        $password = 'a2c456';
        $formData['password1'] = $password;
        $formData['password2'] = $password;
        $form = $this->getProfileIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_identifier]',
            "Votre mot de passe doit avoir au moins 7 caractères alphanumérique et/ou spéciaux."
        );
    }

    public function testProfileIdentifierFormPasswordOnlyNumbers(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/identifiants/modification');
        $formData = $this->getValidUserData();
        // set some values
        $password = '12345678';
        $formData['password1'] = $password;
        $formData['password2'] = $password;
        $form = $this->getProfileIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_identifier]',
            "Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des chiffres."
        );
    }

    public function testProfileIdentifierFormPasswordOnlyLetters(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/identifiants/modification');
        $formData = $this->getValidUserData();
        // set some values
        $password = 'azertyuiop';
        $formData['password1'] = $password;
        $formData['password2'] = $password;
        $form = $this->getProfileIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_identifier]',
            "Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des lettres."
        );
    }

    public function testProfileIdentifierFormPasswordEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/identifiants/modification');
        $formData = $this->getValidUserData();
        // set some values
        $password = '';
        $formData['password1'] = $password;
        $formData['password2'] = $password;
        $form = $this->getProfileIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_identifier]',
            "Le mot de passe ne peut pas être vide."
        );
    }

    public function testProfileIdentifierFormPasswordNotEqual(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/identifiants/modification');
        $formData = $this->getValidUserData();
        // set some values
        $password = 'Azerty789';
        $formData['password2'] = $password;
        $form = $this->getProfileIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_identifier]',
            "Veuillez saisir un mot de passe valide."
        );
    }

    public function testProfileIdentifierFormPasswordWithName(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/identifiants/modification');
        $formData = $this->getValidUserData();
        // set some values
        $password = 'Martin_Dupond';
        $formData['password1'] = $password;
        $formData['password2'] = $password;
        $form = $this->getProfileIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_identifier]',
            "Le mot de passe ne doit pas contenir le prénom et/ou le nom."
        );
    }

    public function testProfileIdentifierFormEmailEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/identifiants/modification');
        $formData = $this->getValidUserData();
        // set some values
        $email = '';
        $formData['email1'] = $email;
        $formData['email2'] = $email;
        $form = $this->getProfileIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_identifier]',
            "L'adresse email ne peut pas être vide."
        );
    }

    public function testProfileIdentifierFormEmailNotEqual(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/identifiants/modification');
        $formData = $this->getValidUserData();
        // set some values
        $email = 'dupond@orange.fr';
        $formData['email2'] = $email;
        $form = $this->getProfileIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_identifier]',
            "Veuillez saisir une adresse email valide."
        );
    }

    public function testProfileIdentifierFormEmailNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/identifiants/modification');
        $formData = $this->getValidUserData();
        // set some values
        $email = 'test';
        $formData['email1'] = $email;
        $formData['email2'] = $email;
        $form = $this->getProfileIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_identifier]',
            "L'adresse email indiqué n'est pas valide."
        );
    }*/

    public function testProfileParameterFormValidDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-parametres');
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_parameter]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Fuseau horaire
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_parameter] *[name*=timeZoneSelected]'),
            "Il doit y avoir un et un seul champ pour le fuseau horaire dans ce formulaire"
        );
        // Abonnement d'offre et de publicité (Newsletters)
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_parameter] *[name*=acceptNewsletters]'),
            "Il doit y avoir un et un seul champ pour accepter la newsletters dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_parameter] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    /*public function getProfileParameterForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('user_profile_parameter[modify]')->form();
        $form['user_profile_parameter[timeZoneSelected]'] = $formData['timezone'];
        $form['user_profile_parameter[acceptNewsletters]'] = $formData['newsletters'];
        return $form;
    }*/
}
