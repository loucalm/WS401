<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class CategoryCrudController extends AbstractAdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular($this->trans('entity.category.singular'))
            ->setEntityLabelInPlural($this->trans('entity.category.plural'))
            ->setDefaultSort(['name' => 'ASC'])
            ->setSearchFields(['name', 'icon', 'color']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', $this->trans('fields.id'))->hideOnForm();
        yield TextField::new('name', $this->trans('fields.name'));
        yield TextField::new('icon', $this->trans('fields.icon'));
        yield TextField::new('color', $this->trans('fields.color'));
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(TextFilter::new('name', $this->trans('fields.name')));
    }
}