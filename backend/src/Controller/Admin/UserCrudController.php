<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class UserCrudController extends AbstractAdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular($this->trans('entity.user.singular'))
            ->setEntityLabelInPlural($this->trans('entity.user.plural'))
            ->setDefaultSort(['email' => 'ASC'])
            ->setSearchFields(['email', 'username']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', $this->trans('fields.id'))->hideOnForm();
        yield EmailField::new('email', $this->trans('fields.email'));
        yield TextField::new('username', $this->trans('fields.username'));
        yield ArrayField::new('roles', $this->trans('fields.roles'))
            ->onlyOnIndex();

        yield ChoiceField::new('roles', $this->trans('fields.roles'))
            ->setChoices([
                $this->trans('choices.role_admin') => 'ROLE_ADMIN',
                $this->trans('choices.role_user') => 'ROLE_USER',
            ])
            ->setFormTypeOption('multiple', true)
            ->setFormTypeOption('expanded', false)
            ->onlyOnForms();
        yield NumberField::new('targetCo2', $this->trans('fields.target_co2'))->setNumDecimals(2);
        yield ChoiceField::new('unitPreference', $this->trans('fields.unit_preference'))
            ->setChoices([
                $this->trans('choices.metric') => 'metric',
                $this->trans('choices.imperial') => 'imperial',
            ]);
        yield NumberField::new('latitude', $this->trans('fields.latitude'))->setNumDecimals(6)->hideOnIndex();
        yield NumberField::new('longitude', $this->trans('fields.longitude'))->setNumDecimals(6)->hideOnIndex();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(
            ChoiceFilter::new('unitPreference', $this->trans('fields.unit_preference'))
                ->setChoices([
                    $this->trans('choices.metric') => 'metric',
                    $this->trans('choices.imperial') => 'imperial',
                ])
        );
    }
}