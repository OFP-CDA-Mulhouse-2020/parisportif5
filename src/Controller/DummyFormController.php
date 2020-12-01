<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DummyFormController extends AbstractController
{
    /**
     * @Route("/account/create", name="formulaire_test")
     */
    public function renderDummyForm(): Response
    {
        return $this->render('dummy_form/index.html.twig', [
            'page_title' => 'Créer un compte',
            'form' => '<form action="" method="get" class="form-example">
            <div class="form-example">
              <label for="name">Enter your name: </label>
              <input type="text" name="name" id="name" required>
            </div>
            <div class="form-example">
              <label for="email">Enter your email: </label>
              <input type="email" name="email" id="email" required>
            </div>
            <div class="form-example">
              <input type="submit" value="Subscribe!">
            </div>
          </form>'
        ]);
    }
}
