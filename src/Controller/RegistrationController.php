<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\FileUploader;
use App\Security\EmailVerifier;
use App\Form\Model\UserFormModel;
use App\Repository\LanguageRepository;
use App\Security\UserLoginAuthenticator;
use App\Form\Handler\RegistrationFormHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Registration\RegistrationFormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/inscription", name="account_register")
     */
    public function registerAccount(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        UserLoginAuthenticator $authenticator,
        LanguageRepository $languageRepository,
        FileUploader $fileUploader
    ): Response {
        $languagesCodes = $request->getLanguages();
        $registrationFormHandler = new RegistrationFormHandler();
        $userFormModel = new UserFormModel();
        $userFormModel = $registrationFormHandler->initializeUserFormModel($userFormModel, $languagesCodes);
        $form = $this->createForm(RegistrationFormType::class, $userFormModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $defaultLanguage = $languageRepository->languageByDefault();
            $filesDirectory["identity_directory"] = $this->getParameter("identity_directory");
            $filesDirectory["residence_directory"] = $this->getParameter("residence_directory");
            $user = $registrationFormHandler->handleForm(
                $form,
                $defaultLanguage,
                $entityManager,
                $passwordEncoder,
                $this->emailVerifier,
                $fileUploader,
                $filesDirectory
            );

            // Add success message
            $this->addFlash(
                'success',
                "Votre compte a été créé ! Son activation sera effective d'ici 24 heures."
            );

            $authentifiedUserResponse = $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );

            if ($authentifiedUserResponse === null) {
                return $this->redirectToRoute('userlogin');
            }
            return $authentifiedUserResponse;
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verification/email", name="account_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        $user = $this->getUser();
        if ($user instanceof User) {
            try {
                $this->emailVerifier->handleEmailConfirmation($request, $user);
            } catch (VerifyEmailExceptionInterface $exception) {
                $this->addFlash('verify_email_error', $exception->getReason());

                return $this->redirectToRoute('account_register');
            }
        }
        // else 'The "getUser" method don\'t return from Security Token Storage a object of User class.'

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('account_register');
    }
}
