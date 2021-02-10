<?php

namespace App\Controller\Admin;

use App\Entity\Bet;
use App\Entity\BetCategory;
use App\Entity\BetSaved;
use App\Entity\Billing;
use App\Entity\Competition;
use App\Entity\Language;
use App\Entity\Location;
use App\Entity\Member;
use App\Entity\MemberRole;
use App\Entity\MemberStatus;
use App\Entity\Run;
use App\Entity\Sport;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Wallet;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        /*// redirect to some CRUD controller
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(OneOfYourCrudController::class)->generateUrl());

        // you can also redirect to different pages depending on the current user
        if ('jane' === $this->getUser()->getUsername()) {
            return $this->redirect('...');
        }

        // you can also render some template to display a proper Dashboard
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        return $this->render('some/path/my-dashboard.html.twig');*/

        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Parisportif5');
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            // this defines the pagination size for all CRUD controllers
            // (each CRUD controller can override this value if needed)
            ->setPaginatorPageSize(30)
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Paris Sportif 5');
        yield MenuItem::linkToCrud('Bets', 'fas fa-list', Bet::class);
        yield MenuItem::linkToCrud('Bets Saved', 'fas fa-list', BetSaved::class);
        yield MenuItem::linkToCrud('Bet Categories', 'fas fa-list', BetCategory::class);
        yield MenuItem::linkToCrud('Users', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Billings', 'fas fa-list', Billing::class);
        yield MenuItem::linkToCrud('Competitions', 'fas fa-list', Competition::class);
        yield MenuItem::linkToCrud('Languages', 'fas fa-list', Language::class);
        yield MenuItem::linkToCrud('Locations', 'fas fa-list', Location::class);
        yield MenuItem::linkToCrud('Members', 'fas fa-list', Member::class);
        yield MenuItem::linkToCrud('Member Roles', 'fas fa-list', MemberRole::class);
        yield MenuItem::linkToCrud('Member Status', 'fas fa-list', MemberStatus::class);
        yield MenuItem::linkToCrud('Runs', 'fas fa-list', Run::class);
        yield MenuItem::linkToCrud('Teams', 'fas fa-list', Team::class);
        yield MenuItem::linkToCrud('Wallets', 'fas fa-list', Wallet::class);
        yield MenuItem::linkToCrud('Sports', 'fas fa-list', Sport::class);
    }
}
