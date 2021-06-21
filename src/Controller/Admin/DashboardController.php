<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Experience;
use App\Entity\Field;
use App\Entity\Level;
use App\Entity\OwnSkill;
use App\Entity\SearchedSkill;
use App\Entity\Skills;
use App\Entity\Training;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);
        return  $this->redirect($routeBuilder->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Shareskills OneToMany')
            ->renderSidebarMinimized();
            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Skills', 'fas fa-graduation-cap', OwnSkill::class);
        yield MenuItem::linkToCrud('Search Skills', 'fas fa-search', searchedSkill::class);
        yield MenuItem::linkToCrud('Field', 'fas fa-tags', Field::class);
        yield MenuItem::linkToCrud('Level', 'fas fa-layer-group', Level::class);
        yield MenuItem::linkToCrud('Trainning', 'fas fa-book', Training::class);
        yield MenuItem::linkToCrud('Experience', 'fas fa-award', Experience::class);
        yield MenuItem::linkToCrud('User', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('City', 'fas fa-city', City::class);
        yield MenuItem::linkToCrud('Country', 'fas fa-globe-africa', Country::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
