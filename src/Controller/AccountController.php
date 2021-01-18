<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Account\AccountDocumentType;
use App\Form\Account\AccountParameterType;
use App\Form\Account\AccountPersonalDataType;
use App\Form\Account\AccountUpdateIdentifierType;
use App\Form\Account\AccountUpdatePasswordType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/mon-compte/mes-informations", name="user_profile")
     */
    public function editPersonalDatas(Request $request): Response
    {
        //$user = $this->getUser();
        $user = new User();
        $user
            ->setCivility(null)
            ->setFirstName("Tintin")
            ->setLastName("Dupont")
            ->setBillingAddress("1 avenue st martin")
            ->setBillingCity("Colmar")
            ->setBillingPostcode("68000")
            ->setBillingCountry("FR")
            ->setBirthDate(new \DateTimeImmutable("2000-10-10"))
            ->setPassword("Azerty78")
            ->setEmail("dupond.t@orange.fr")
            ->setTimeZoneSelected("Europe/Paris");

        $form = $this->createForm(AccountPersonalDataType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-informations');
        }

        return $this->render('account/index.html.twig', [
            'page_title' => "Données personnelles",
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/modifier/mot-de-passe", name="user_password")
     */
    public function editPassword(Request $request): Response
    {
        $user = new User();
        $user
            ->setCivility(null)
            ->setFirstName("Tintin")
            ->setLastName("Dupont")
            ->setBillingAddress("1 avenue st martin")
            ->setBillingCity("Colmar")
            ->setBillingPostcode("68000")
            ->setBillingCountry("FR")
            ->setBirthDate(new \DateTimeImmutable("2000-10-10"))
            ->setPassword("Azerty78")
            ->setEmail("dupond.t@orange.fr")
            ->setTimeZoneSelected("Europe/Paris");

        $form = $this->createForm(AccountUpdatePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-informations');
        }

        return $this->render('account/update.html.twig', [
            'page_title' => "Modifier le mot de passe du compte",
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/modifier/identifiant", name="user_identifier")
     */
    public function editIdentifier(Request $request): Response
    {
        $user = new User();
        $user
            ->setCivility(null)
            ->setFirstName("Tintin")
            ->setLastName("Dupont")
            ->setBillingAddress("1 avenue st martin")
            ->setBillingCity("Colmar")
            ->setBillingPostcode("68000")
            ->setBillingCountry("FR")
            ->setBirthDate(new \DateTimeImmutable("2000-10-10"))
            ->setPassword("Azerty78")
            ->setEmail("dupond.t@orange.fr")
            ->setTimeZoneSelected("Europe/Paris");

        $form = $this->createForm(AccountUpdateIdentifierType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-informations');
        }

        return $this->render('account/update.html.twig', [
            'page_title' => "Modifier l'identifiant du compte",
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/mes-documents", name="user_document")
     */
    public function editDocuments(Request $request): Response
    {
        $user = new User();
        $user
            ->setCivility(null)
            ->setFirstName("Tintin")
            ->setLastName("Dupont")
            ->setBillingAddress("1 avenue st martin")
            ->setBillingCity("Colmar")
            ->setBillingPostcode("68000")
            ->setBillingCountry("FR")
            ->setBirthDate(new \DateTimeImmutable("2000-10-10"))
            ->setPassword("Azerty78")
            ->setEmail("dupond.t@orange.fr")
            ->setTimeZoneSelected("Europe/Paris");

        $form = $this->createForm(AccountDocumentType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-documents');
        }

        return $this->render('account/index.html.twig', [
            'page_title' => "Vos documents",
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/mes-parametres", name="user_parameter")
     */
    public function editParameters(Request $request): Response
    {
        $user = new User();
        $user
            ->setCivility(null)
            ->setFirstName("Tintin")
            ->setLastName("Dupont")
            ->setBillingAddress("1 avenue st martin")
            ->setBillingCity("Colmar")
            ->setBillingPostcode("68000")
            ->setBillingCountry("FR")
            ->setBirthDate(new \DateTimeImmutable("2000-10-10"))
            ->setPassword("Azerty78")
            ->setEmail("dupond.t@orange.fr")
            ->setTimeZoneSelected("Europe/Paris");

        $form = $this->createForm(AccountParameterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-parametres');
        }

        return $this->render('account/index.html.twig', [
            'page_title' => "Vos paramètres",
            'form' => $form->createView()
        ]);
    }
}
