<?php

namespace App\Controller;

use App\Repository\RunRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RunsController extends AbstractController
{
    /**
     * @Route("/{competitionSlug}/{competition_id}", name="runs")
     */
    public function index(
        RunRepository $runRepository
    ): Response {
        $run = $runRepository
            ->findAll();
        return $this->render('runs/runs.html.twig', [
            'controller_name' => 'RunsController',
            'page_title' => 'Listes des runs',
            'runs' => $run
        ]);
    }
}
