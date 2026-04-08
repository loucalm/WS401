<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractAdminCrudController extends AbstractCrudController
{
    public function __construct(
        protected readonly TranslatorInterface $translator,
    ) {
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPaginatorPageSize(25);
    }

    protected function trans(string $key, array $parameters = []): string
    {
        return $this->translator->trans($key, $parameters, 'admin');
    }
}