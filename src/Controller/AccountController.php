<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\FileUploader;
use App\Security\EmailVerifier;
use App\Form\Model\UserFormModel;
use App\Form\Account as AccountForm;
use App\Form\Handler as FormHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    //todo : ajouter fond et historique paris

    protected function initializeUserFormModel(User $user): UserFormModel
    {
        $userFormModel = UserFormModel::createFromUser($user);
        return $userFormModel;
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

        $userFormModel = $this->initializeUserFormModel($user);

        $form = $this->createForm(AccountForm\AccountPersonalDataFormType::class, $userFormModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-informations');
            $accountPersonalDataFormHandler = new FormHandler\AccountPersonalDataFormHandler();
            $entityManager = $this->getDoctrine()->getManager();
            $accountPersonalDataFormHandler->handleForm(
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

        $userFormModel = $this->initializeUserFormModel($user);

        $form = $this->createForm(AccountForm\AccountUpdatePasswordFormType::class, $userFormModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-informations');
            $accountFormHandler = new FormHandler\AccountUpdatePasswordFormHandler();
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

        $userFormModel = $this->initializeUserFormModel($user);

        $form = $this->createForm(AccountForm\AccountUpdateIdentifierFormType::class, $userFormModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-informations');
            $accountFormHandler = new FormHandler\AccountUpdateIdentifierFormHandler();
            $entityManager = $this->getDoctrine()->getManager();
            $accountFormHandler->handleForm(
                $form,
                $user,
                $entityManager,
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
    public function editDocuments(Request $request, FileUploader $fileUploader): Response
    {
        // usually you'll want to make sure the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var User $user */
        $user = $this->getUser();

        $userFormModel = $this->initializeUserFormModel($user);
        /*$userFormModel->setIdentityDocumentFileName(
            FormHandler\AccountDocumentFormHandler::getBasenameFromFormated(
                $userFormModel->getIdentityDocumentFileName()
            )
        );
        $userFormModel->setResidenceProofFileName(
            FormHandler\AccountDocumentFormHandler::getBasenameFromFormated(
                $userFormModel->getResidenceProofFileName()
            )
        );*/

        $form = $this->createForm(AccountForm\AccountDocumentFormType::class, $userFormModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-documents');
            $accountFormHandler = new FormHandler\AccountDocumentFormHandler();
            $entityManager = $this->getDoctrine()->getManager();
            $filesDirectories["identity_directory"] = $this->getParameter("identity_directory");
            $filesDirectories["residence_directory"] = $this->getParameter("residence_directory");
            $accountFormHandler->handleForm(
                $form,
                $user,
                $entityManager,
                $fileUploader,
                $filesDirectories
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

        $userFormModel = $this->initializeUserFormModel($user);

        $form = $this->createForm(AccountForm\AccountParameterFormType::class, $userFormModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //return new RedirectResponse('/mon-compte/mes-parametres');
            $accountFormHandler = new FormHandler\AccountParameterFormHandler();
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
