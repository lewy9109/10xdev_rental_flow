<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PropertyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Property::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Property')
            ->setEntityLabelInPlural('Properties');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name')
            ->setLabel('Name/Label');
        yield TextField::new('address');
        yield TextField::new('apartmentNumber')
            ->setLabel('Apartment number');
        yield NumberField::new('size')
            ->setNumDecimals(2)
            ->setLabel('Size (m²)');
        yield TextField::new('type')
            ->setFormTypeOption('required', false);
    }
}
