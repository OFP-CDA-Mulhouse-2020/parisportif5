<?php

namespace App\Controller;

use App\Entity\Language;
use App\Entity\User;
use App\Entity\Wallet;
use App\Form\User\Registration\UserRegistrationType;
use App\Repository\LanguageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegistrationController extends AbstractController
{
    private UserPasswordEncoderInterface $passwordEncoder;
    /** @const string ICU_DEFAULT_LANGUAGE_CODE */
    public const ICU_DEFAULT_LANGUAGE_CODE = 'fr_FR';

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /** @param array<int,string> $languagesCodes */
    protected function getICUPreferredLanguageCode(array $languagesCodes): string
    {
        if (empty($languagesCodes)) {
            return self::ICU_DEFAULT_LANGUAGE_CODE;
        }
        $icuPreferredLanguages = array_map(function (string $language) {
            if (mb_strlen($language) === 4) {
                return $language;
            }
        }, $languagesCodes);
        if (empty($icuPreferredLanguages)) {
            $icuPreferredLanguages[0] = $languagesCodes[0];
        }
        return $icuPreferredLanguages[0] ?? self::ICU_DEFAULT_LANGUAGE_CODE;
    }

    protected function getBrowserLanguage(Request $request, LanguageRepository $languageRepository): Language
    {
        $languagesCodes = $request->getLanguages();
        $preferredLanguageCode = $this->getICUPreferredLanguageCode($languagesCodes);
        $userLanguage = $languageRepository->findOneByLanguageCode($preferredLanguageCode);
        if (is_null($userLanguage)) {
            $userLanguage = $languageRepository->languageByDefault();
        }
        return $userLanguage;
    }

    /**
     * @Route("/inscription", name="user_registration")
     */
    public function register(Request $request, LanguageRepository $languageRepository): Response
    {
        $user = new User();

        $userLanguage = $this->getBrowserLanguage($request, $languageRepository);

        $browserTimezone = $userLanguage->getCapitalTimeZone();

        if (!empty($browserTimezone)) {
            $user->setTimeZoneSelected($browserTimezone);
        }

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
            $user
                ->setLanguage($userLanguage)
                ->setWallet($userWallet);
            // Persist user
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Add success message
            $this->addFlash(
                'success',
                "Votre compte a été créé ! Son activation sera effective d'ici 24 heures."
            );

            return $this->redirectToRoute('main');
        }

        return $this->render('user_registration/index.html.twig', [
            'page_title' => 'Créer un compte',
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/main", name="main")
     */
    public function main(): Response
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
