<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wallet;
use App\Form\UserRegistrationType;
use App\Repository\LanguageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegistrationController extends AbstractController
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function getICUPreferredLanguageCode(Request $request): string
    {
        $languagesCodes = $request->getLanguages();
        $default = 'fr_FR';
        if (empty($languagesCodes)) {
            return $default;
        }
        $icuPreferredLanguages = array_map(function (string $language) {
            if (mb_strlen($language) === 4) {
                return $language;
            }
        }, $languagesCodes);
        if (empty($icuPreferredLanguages)) {
            $icuPreferredLanguages[0] = $languagesCodes[0];
        }
        return $icuPreferredLanguages[0] ?? $default;
    }

    /**
     * @Route("/inscription", name="user_registration")
     */
    public function registrationForm(Request $request, LanguageRepository $languageRepository): Response
    {
        $user = new User();

        $form = $this->createForm(UserRegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$user = $form->getData();

            // Encode the new users password
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

            // Set user roles
            $user->setRoles(['ROLE_USER']);

            // Set others user values
            $userWallet = new Wallet();
            $userWallet
                ->setUser($user)
                ->setAmount(0);
            $preferredLanguageCode = $this->getICUPreferredLanguageCode($request);
            $userLanguage = $languageRepository->findOneByLanguageCode($preferredLanguageCode);
            if (is_null($userLanguage)) {
                $userLanguage = $languageRepository->languageByDefault();
            }
            $selectedTimezone = $userLanguage->getCapitalTimeZone() ?? 'UTC';
            $user
                ->setLanguage($userLanguage)
                ->setWallet($userWallet)
                ->setTimeZoneSelected($selectedTimezone);
            // Persist user
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Add success message
            $this->addFlash(
                'success',
                "Votre compte a été créé ! Son activation sera effective d'ici 24 heures."
            );

            return $this->redirectToRoute('Connexion');
        }

        return $this->render('user_registration/index.html.twig', [
            'page_title' => 'Créer un compte',
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/main", name="main")
     */
    public function mainPage(): Response
    {
        //Request $request
        //$preferredLanguageCode = $request->getPreferredLanguage();
        //$data = $request->getLanguages();
        //$this->addFlash('notice', $preferredLanguageCode . var_dump($data));
        return $this->render('base.html.twig', [
            'page_title' => 'Accueil'
        ]);
    }
}
