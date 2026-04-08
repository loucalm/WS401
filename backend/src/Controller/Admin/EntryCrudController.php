<?php

namespace App\Controller\Admin;

use App\Entity\Entry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class EntryCrudController extends AbstractAdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return Entry::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular($this->trans('entity.entry.singular'))
            ->setEntityLabelInPlural($this->trans('entity.entry.plural'))
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['id', 'owner.email', 'owner.username']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', $this->trans('fields.id'))->hideOnForm();
        yield AssociationField::new('owner', $this->trans('fields.owner'))->autocomplete();
        yield NumberField::new('value', $this->trans('fields.value'))->setNumDecimals(2)->hideOnForm();
        yield NumberField::new('totalCo2', $this->trans('fields.total_co2'))->setNumDecimals(2)->hideOnForm();
        yield DateTimeField::new('createdAt', $this->trans('fields.created_at'))->hideOnForm();
        yield TextareaField::new('details', $this->trans('fields.details'))
            ->hideOnIndex()
            ->hideOnForm()
            ->formatValue(static function (?array $value): string {
                if ($value === null || $value === []) {
                    return '';
                }

                return (string) json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            });
        yield CollectionField::new('entryItems', $this->trans('fields.entry_items'))
            ->onlyOnDetail();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('owner', $this->trans('fields.owner')))
            ->add(DateTimeFilter::new('createdAt', $this->trans('fields.created_at')));
    }
}