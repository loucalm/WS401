<?php

namespace App\Controller\Admin;

use App\Entity\ActivityType;
use App\Entity\Category;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->redirectToRoute('admin', ['crudAction' => 'index', 'crudControllerFqcn' => UserCrudController::class]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('N2E Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);
        yield MenuItem::section('Activites');
        yield MenuItem::linkToCrud('Activity Types', 'fa fa-list', ActivityType::class);
        yield MenuItem::linkToCrud('Categories', 'fa fa-tags', Category::class);
    }
}
