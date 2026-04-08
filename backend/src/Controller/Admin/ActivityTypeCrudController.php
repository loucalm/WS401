<?php

namespace App\Controller\Admin;

use App\Entity\ActivityType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class ActivityTypeCrudController extends AbstractAdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return ActivityType::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular($this->trans('entity.activity_type.singular'))
            ->setEntityLabelInPlural($this->trans('entity.activity_type.plural'))
            ->setDefaultSort(['category' => 'ASC', 'name' => 'ASC'])
            ->setSearchFields(['name', 'subCategory', 'diet', 'unitLabel', 'category.name']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', $this->trans('fields.id'))->hideOnForm();
        yield TextField::new('name', $this->trans('fields.name'));
        yield AssociationField::new('category', $this->trans('fields.category'))->autocomplete();
        yield TextField::new('subCategory', $this->trans('fields.sub_category'));
        yield TextField::new('diet', $this->trans('fields.diet'));
        yield NumberField::new('co2Factor', $this->trans('fields.co2_factor'))->setNumDecimals(2);
        yield TextField::new('unitLabel', $this->trans('fields.unit_label'));
        yield TextField::new('icon', $this->trans('fields.icon'))->hideOnIndex();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('category', $this->trans('fields.category')))
            ->add(TextFilter::new('subCategory', $this->trans('fields.sub_category')))
            ->add(TextFilter::new('diet', $this->trans('fields.diet')));
    }
}