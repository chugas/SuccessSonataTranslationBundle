<?php

namespace Ibrows\SonataTranslationBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;

class ORMTranslationAdmin extends TranslationAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('key', 'doctrine_orm_string')
            ->add('domain', 'doctrine_orm_string')
        ;
    }
}
