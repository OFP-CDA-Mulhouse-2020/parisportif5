<?php

namespace App\Controller;

use App\Entity\Language;
use App\Entity\User;
use App\Entity\Wallet;
use App\Form\UserRegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/inscription", name="user_registration")
     */
    public function registrationForm(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $userWallet = new Wallet();
            $userWallet
                ->setUser($user)
                ->setAmount(0);
            $userLanguage = new Language();
            $userLanguage
                ->setName('français')
                ->setCountry('france')
                ->setCode('fr_FR')
                ->setDateFormat('d/m/Y')
                ->setTimeFormat('h:i:s')
                ->setTimeZone('Europe/Paris');
            /*$preferredLanguageCode = $this->getICUPreferredLanguageCode($request);
            $userLanguage => findOneByLanguageCode($preferredLanguageCode)
            if (is_null($userLanguage)) {
                $userLanguage => findOneByDefault('fr_FR')
            }*/
            $user
                ->setLanguage($userLanguage)
                ->setWallet($userWallet)
                ->setTimeZoneSelected($userLanguage->getTimeZone());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Votre compte a été créé ! Son activation sera effective d'ici 24 heures."
            );
            return $this->redirectToRoute('main');
        }
        return $this->render('user/new.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Créer un compte',
            'form' => $form->createView()
        ]);
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
     * @Route("/main", name="main")
     */
    public function mainPage(): Response
    {
        //Request $request
        //$preferredLanguageCode = $request->getPreferredLanguage();
        //$data = $request->getLanguages();
        //$this->addFlash('notice', $preferredLanguageCode . var_dump($data));
        return $this->render('base.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Accueil'
        ]);
    }
}
