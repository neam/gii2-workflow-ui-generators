<?php

namespace neam\yii_workflow_ui_giiant_generator\crud;

use Yii;

/**
 * Yii Workflow UI Generator.
 * @author Fredrik Wollsén <fredrik@neam.se>
 * @since 1.0
 */
class Generator extends \schmunk42\giiant\crud\Generator
{

    public function getName()
    {
        return 'Yii Workflow UI CRUD Generator';
    }

    public function getDescription()
    {
        return 'This generator generates a yii-workflow-ui compatible controller and views that implement CRUD (Create, Read, Update, Delete)
            operations for the specified data model.';
    }

}
