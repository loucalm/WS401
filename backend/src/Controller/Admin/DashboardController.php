<?php

namespace App\Controller\Admin;

use App\Entity\ActivityType;
use App\Entity\Category;
use App\Entity\Entry;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function index(): Response
    {
        $categoryRepository = $this->entityManager->getRepository(Category::class);
        $activityTypeRepository = $this->entityManager->getRepository(ActivityType::class);
        $userRepository = $this->entityManager->getRepository(User::class);
        $entryRepository = $this->entityManager->getRepository(Entry::class);

        $stats = [
            'categories' => $categoryRepository->count([]),
            'activityTypes' => $activityTypeRepository->count([]),
            'users' => $userRepository->count([]),
            'entries' => $entryRepository->count([]),
        ];

        $recentEntries = $entryRepository->findBy([], ['createdAt' => 'DESC'], 6);

        $quickLinks = [
            [
                'label' => $this->trans('menu.categories'),
                'description' => $this->trans('dashboard.cards.categories'),
                'icon' => 'fa-solid fa-layer-group',
                'count' => $stats['categories'],
                'url' => $this->adminUrl(CategoryCrudController::class),
            ],
            [
                'label' => $this->trans('menu.activity_types'),
                'description' => $this->trans('dashboard.cards.activity_types'),
                'icon' => 'fa-solid fa-seedling',
                'count' => $stats['activityTypes'],
                'url' => $this->adminUrl(ActivityTypeCrudController::class),
            ],
            [
                'label' => $this->trans('menu.users'),
                'description' => $this->trans('dashboard.cards.users'),
                'icon' => 'fa-solid fa-users',
                'count' => $stats['users'],
                'url' => $this->adminUrl(UserCrudController::class),
            ],
            [
                'label' => $this->trans('menu.entries'),
                'description' => $this->trans('dashboard.cards.entries'),
                'icon' => 'fa-solid fa-chart-line',
                'count' => $stats['entries'],
                'url' => $this->adminUrl(EntryCrudController::class),
            ],
        ];

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'quickLinks' => $quickLinks,
            'recentEntries' => $recentEntries,
            'currentLocale' => $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr',
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTranslationDomain('admin')
            ->setTitle($this->trans('dashboard.title'))
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard($this->trans('menu.dashboard'), 'fa-solid fa-chart-pie');

        yield MenuItem::section($this->trans('menu.catalog'));
        yield MenuItem::linkTo(CategoryCrudController::class, $this->trans('menu.categories'), 'fa-solid fa-layer-group');
        yield MenuItem::linkTo(ActivityTypeCrudController::class, $this->trans('menu.activity_types'), 'fa-solid fa-seedling');

        yield MenuItem::section($this->trans('menu.community'));
        yield MenuItem::linkTo(UserCrudController::class, $this->trans('menu.users'), 'fa-solid fa-users');
        yield MenuItem::linkTo(EntryCrudController::class, $this->trans('menu.entries'), 'fa-solid fa-chart-line');

        yield MenuItem::section($this->trans('menu.international'));
        yield MenuItem::linkToRoute($this->trans('menu.french'), 'fa-solid fa-language', 'admin_locale', ['locale' => 'fr']);
        yield MenuItem::linkToRoute($this->trans('menu.english'), 'fa-solid fa-globe', 'admin_locale', ['locale' => 'en']);
        yield MenuItem::linkToLogout($this->trans('menu.logout'), 'fa-solid fa-right-from-bracket');
    }

    private function trans(string $key, array $parameters = []): string
    {
        return $this->translator->trans($key, $parameters, 'admin');
    }

    private function adminUrl(string $crudController): string
    {
        return (clone $this->adminUrlGenerator)
            ->setDashboard(DashboardController::class)
            ->setController($crudController)
            ->setAction(Crud::PAGE_INDEX)
            ->generateUrl();
    }
}
