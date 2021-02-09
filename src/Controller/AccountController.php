<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Form\Handler\AccountFormHandler;
use App\Form\Account\AccountDocumentFormType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Account\AccountParameterFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Account\AccountPersonalDataFormType;
use App\Form\Account\AccountUpdatePasswordFormType;
use App\Form\Account\AccountUpdateIdentifierFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/mon-compte/mes-informations", name="account_profile")
     */
    public function editPersonalDatas(Request $request): Response
    {
        // usually you'll want to make sure the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(AccountPersonalDataFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-informations');
            $accountFormHandler = new AccountFormHandler();
            $entityManager = $this->getDoctrine()->getManager();
            $accountFormHandler->handleForm(
                $form,
                $user,
                $entityManager
            );

             // Add success message
             $this->addFlash(
                 'success',
                 "Vos informations personnelles ont été mis à jour."
             );
        }

        return $this->render('account/index.html.twig', [
            'page_title' => "Données personnelles",
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/modifier/mot-de-passe", name="account_password")
     */
    public function editPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        // usually you'll want to make sure the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(AccountUpdatePasswordFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-informations');
            $accountFormHandler = new AccountFormHandler();
            $entityManager = $this->getDoctrine()->getManager();
            $accountFormHandler->handleForm(
                $form,
                $user,
                $entityManager,
                $passwordEncoder
            );

             // Add success message
             $this->addFlash(
                 'success',
                 "Votre mot de passe a été mis à jour."
             );
        }

        return $this->render('account/update.html.twig', [
            'page_title' => "Modifier le mot de passe du compte",
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/modifier/identifiant", name="account_identifier")
     */
    public function editIdentifier(Request $request): Response
    {
        // usually you'll want to make sure the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(AccountUpdateIdentifierFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-informations');
            $accountFormHandler = new AccountFormHandler();
            $entityManager = $this->getDoctrine()->getManager();
            $accountFormHandler->handleForm(
                $form,
                $user,
                $entityManager,
                null,
                $this->emailVerifier
            );

             // Add success message
             $this->addFlash(
                 'success',
                 "Votre identifiant a été mis à jour."
             );
        }

        return $this->render('account/update.html.twig', [
            'page_title' => "Modifier l'identifiant du compte",
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/mes-documents", name="account_document")
     */
    public function editDocuments(Request $request): Response
    {
        // usually you'll want to make sure the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(AccountDocumentFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-documents');
            $accountFormHandler = new AccountFormHandler();
            $entityManager = $this->getDoctrine()->getManager();
            $accountFormHandler->handleForm(
                $form,
                $user,
                $entityManager
            );

             // Add success message
             $this->addFlash(
                 'success',
                 "Vos documents ont été mis à jour."
             );
        }

        return $this->render('account/index.html.twig', [
            'page_title' => "Vos documents",
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/mes-parametres", name="account_parameter")
     */
    public function editParameters(Request $request): Response
    {
        // usually you'll want to make sure the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(AccountParameterFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-parametres');
            $accountFormHandler = new AccountFormHandler();
            $entityManager = $this->getDoctrine()->getManager();
            $accountFormHandler->handleForm(
                $form,
                $user,
                $entityManager
            );

             // Add success message
             $this->addFlash(
                 'success',
                 "Vos paramètres ont été mis à jour."
             );
        }

        return $this->render('account/index.html.twig', [
            'page_title' => "Vos paramètres",
            'form' => $form->createView()
        ]);
    }
}
