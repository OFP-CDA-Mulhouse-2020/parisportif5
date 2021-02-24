<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\RunRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Symfony\Component\DomCrawler\Form;
use App\Repository\BetCategoryRepository;
use App\Repository\CompetitionRepository;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \BettingController
 */
final class BettingControllerTest extends WebTestCase
{
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

    protected function getValidBettingData(): array
    {
        $teamRepository = static::$container->get(TeamRepository::class);
        // retrieve the test team
        $testTeam = $teamRepository
            ->findOneBy(['name' => 'Racing Club de Strasbourg Alsace']);
        $data = [
            'result' => $testTeam->getId(),
            'amount' => 10
        ];
        return $data;
    }

    protected function getBettingForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->filter('form *[name*=bettingOn][type=submit]')->form();
        $formName = $form->getName();
        $form->disableValidation();
        $form[$formName . '[amount]'] = $formData['amount'];
        $form[$formName . '[result]'] = $formData['result'];
        return $form;
    }

    protected function getBetCategoryUrlParameter(bool $onCompetition): string
    {
        $betCategoryRepository = static::$container->get(BetCategoryRepository::class);
        // retrieve the test betCategory
        $testBetCategory = $betCategoryRepository
            ->findOneBy([
                'name' => "result",
                'onCompetition' => $onCompetition
            ])
        ;
        return 'paris-betCategorySlug-' . $testBetCategory->getId();
    }

    protected function getRunBettingPageUrl(): string
    {
        $runRepository = static::$container->get(RunRepository::class);
        // retrieve the test run
        $testRun = $runRepository
            ->findOneBy(['name' => "Match 1 vs2"]);
        // url
        $url = '/sportSlug/competitionSlug/eventSlug/runSlug-' . $testRun->getId();
        $url .= '/' . $this->getBetCategoryUrlParameter(false);
        return $url;
    }

    protected function getCompetitionBettingPageUrl(): string
    {
        $competitionRepository = static::$container->get(CompetitionRepository::class);
        // retrieve the test competition
        $testCompetition = $competitionRepository
            ->findOneBy(['name' => "Championnat1"]);
        // url
        $url = '/sportSlug/competitionSlug-' . $testCompetition->getId();
        $url .= '/' . $this->getBetCategoryUrlParameter(true);
        return $url;
    }

    // Tests fonctionnels d'intégrations

    // => Without User

    public function testRunBettingFormPageWithoutUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRunBettingPageUrl());
        $formData = $this->getValidBettingData();
        // set some values
        $form = $this->getBettingForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        // redirection
        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/connexion');
        $crawler = $client->followRedirect();
    }

    public function testCompetitionBettingFormPageWithoutUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getCompetitionBettingPageUrl());
        $formData = $this->getValidBettingData();
        // set some values
        $form = $this->getBettingForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        // redirection
        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/connexion');
        $crawler = $client->followRedirect();
    }

    // => With User

    // ==> Run

    public function testRunBettingFormValidPage(): void
    {
        $client = $this->loginTestUser();
        $client->request('GET', $this->getRunBettingPageUrl());
        $this->assertResponseStatusCodeSame(200);
    }

    public function testRunBettingFormValidDisplay(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getRunBettingPageUrl());
        $formName = $crawler->filter('form *[name*=bettingOn][type=submit]')->form()->getName();
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . ']'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Montant
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=amount]'),
            "Il doit y avoir un et un seul champ pour le montant dans ce formulaire"
        );
        // Résultat du paris attendu
        $this->assertGreaterThanOrEqual(
            2,
            count($crawler->filter('form[name=' . $formName . '] input[name*=result][type="radio"]')),
            "Il doit y avoir au moins deux bouton radio pour le résultat du paris dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    public function testRunBettingFormInvalidAmountWithZero(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getRunBettingPageUrl());
        $formData = $this->getValidBettingData();
        // set some values
        $amount = 0;
        $formData['amount'] = $amount;
        $form = $this->getBettingForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le montant du paris ne peut pas être négatif ou zéro."
        );
    }

    public function testRunBettingFormInvalidAmountWithNegative(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getRunBettingPageUrl());
        $formData = $this->getValidBettingData();
        // set some values
        $amount = -1;
        $formData['amount'] = $amount;
        $form = $this->getBettingForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le montant du paris ne peut pas être négatif ou zéro."
        );
    }

    public function testRunBettingFormInvalidAmountWithEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getRunBettingPageUrl());
        $formData = $this->getValidBettingData();
        // set some values
        $amount = null;
        $formData['amount'] = $amount;
        $form = $this->getBettingForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le montant du paris ne peut pas être vide."
        );
    }

    public function testRunBettingFormInvalidAmountWithString(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getRunBettingPageUrl());
        $formData = $this->getValidBettingData();
        // set some values
        $amount = 'string';
        $formData['amount'] = $amount;
        $form = $this->getBettingForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Veuillez saisir un montant avec des chiffres."
        );
    }

    public function testRunBettingFormInvalidResultWithEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getRunBettingPageUrl());
        $formData = $this->getValidBettingData();
        // set some values
        $result = ' ';
        $formData['result'] = $result;
        $form = $this->getBettingForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "This value is not valid."
        );
    }

    // ==> Competition

    public function testCompetitionBettingFormValidPage(): void
    {
        $client = $this->loginTestUser();
        $client->request('GET', $this->getCompetitionBettingPageUrl());
        $this->assertResponseStatusCodeSame(200);
    }

    public function testCompetitionBettingFormValidDisplay(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getCompetitionBettingPageUrl());
        $formName = $crawler->filter('form *[name*=bettingOn][type=submit]')->form()->getName();
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . ']'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Montant
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=amount]'),
            "Il doit y avoir un et un seul champ pour le montant dans ce formulaire"
        );
        // Résultat du paris attendu
        $this->assertGreaterThanOrEqual(
            2,
            count($crawler->filter('form[name=' . $formName . '] input[name*=result][type="radio"]')),
            "Il doit y avoir au moins deux bouton radio pour le résultat du paris dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    public function testCompetitionBettingFormInvalidAmountWithZero(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getCompetitionBettingPageUrl());
        $formData = $this->getValidBettingData();
        // set some values
        $amount = 0;
        $formData['amount'] = $amount;
        $form = $this->getBettingForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le montant du paris ne peut pas être négatif ou zéro."
        );
    }

    public function testCompetitionBettingFormInvalidAmountWithNegative(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getCompetitionBettingPageUrl());
        $formData = $this->getValidBettingData();
        // set some values
        $amount = -1;
        $formData['amount'] = $amount;
        $form = $this->getBettingForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le montant du paris ne peut pas être négatif ou zéro."
        );
    }

    public function testCompetitionBettingFormInvalidAmountWithEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getCompetitionBettingPageUrl());
        $formData = $this->getValidBettingData();
        // set some values
        $amount = null;
        $formData['amount'] = $amount;
        $form = $this->getBettingForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le montant du paris ne peut pas être vide."
        );
    }

    public function testCompetitionBettingFormInvalidAmountWithString(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getCompetitionBettingPageUrl());
        $formData = $this->getValidBettingData();
        // set some values
        $amount = 'string';
        $formData['amount'] = $amount;
        $form = $this->getBettingForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Veuillez saisir un montant avec des chiffres."
        );
    }

    public function testCompetitionBettingFormInvalidResultWithEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', $this->getCompetitionBettingPageUrl());
        $formData = $this->getValidBettingData();
        // set some values
        $result = ' ';
        $formData['result'] = $result;
        $form = $this->getBettingForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "This value is not valid."
        );
    }
}
