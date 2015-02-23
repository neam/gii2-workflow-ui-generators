<?php

namespace neam\workflow_ui_generators\yii1_tests;

use Yii;

/**
 * Yii Workflow UI Generator.
 * @author Fredrik Wollsén <fredrik@neam.se>
 * @since 1.0
 */
class Generator extends \neam\workflow_ui_generators\yii1_crud\Generator
{

    public function getName()
    {
        return 'Yii Workflow UI Codeception Test Generator';
    }

    public function getDescription()
    {
        return 'This generator generates Codeception tests to use with yii-workflow-ui.';
    }

}
