<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Profile\UserProfileDocumentType;
use App\Form\Profile\UserProfileIdentifierType;
use App\Form\Profile\UserProfileParameterType;
use App\Form\Profile\UserProfilePersonalDataType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    /**
     * @Route("/profil", name="user_profile")
     */
    public function profileIndex(Request $request): Response
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

        return $this->render('user_profile/index.html.twig', [
            'page_title' => 'Profil du compte',
            'user' => $user
        ]);
    }

    /**
     * @Route("/mon-compte/mes-informations", name="user_profile")
     */
    public function profilePersonalDataForm(Request $request): Response
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

        $form = $this->createForm(UserProfilePersonalDataType::class, $user);

        //dd($form, $user);
        if ($form->isSubmitted() && $form->isValid()) {
            //$user = $form->getData();
            dd($user);
        }

        return $this->render('user_profile/update.html.twig', [
            'page_title' => "Données personnelles",
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/identifiants/modification", name="user_identifier")
     */
    public function profileIdentifierForm(Request $request): Response
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

        $form = $this->createForm(UserProfileIdentifierType::class, $user);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($user);
        }

        return $this->render('user_profile/update.html.twig', [
            'page_title' => "Modification des identifiants de connexion au compte",
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/mes-documents", name="user_document")
     */
    public function profileDocumentForm(Request $request): Response
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

        $form = $this->createForm(UserProfileDocumentType::class, $user);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($user);
        }

        return $this->render('user_profile/update.html.twig', [
            'page_title' => "Vos documents",
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/mes-parametres", name="user_parameter")
     */
    public function profileParameterForm(Request $request): Response
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

        $form = $this->createForm(UserProfileParameterType::class, $user);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($user);
        }

        return $this->render('user_profile/update.html.twig', [
            'page_title' => "Vos paramètres",
            'form' => $form->createView()
        ]);
    }
}
