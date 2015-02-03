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
    public function rules()
    {
        $rules = parent::rules();

        // Alter the rule that restricts model classes to yii2 active records so that we can use yii 1 active records
        foreach ($rules as &$rule) {
            if ($rule[0][0] === "modelClass" && $rule[1] === "validateClass") {
                $rule["params"]["extends"] = "\CActiveRecord";
            }
        }

        return $rules;
    }

    /**
     * Checks if yii 1 model class is valid
     */
    public function validateModelClass()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $table=$class::model()->getMetaData()->tableSchema;
        $pk=$table->primaryKey;
        if (empty($pk)) {
            $this->addError('modelClass', "The table associated with $class must have primary key(s).");
        }
    }

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
