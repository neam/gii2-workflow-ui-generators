<?php

namespace neam\gii2_workflow_ui_generators\angular_crud;

use Yii;
use yii\gii\CodeFile;
use yii\helpers\Inflector;


/**
 * Yii Workflow UI Generator.
 * @author Fredrik Wollsén <fredrik@neam.se>
 * @since 1.0
 */
class Generator extends \neam\gii2_workflow_ui_generators\yii1_crud\Generator
{

    public function getName()
    {
        return 'AngularJS Workflow UI CRUD Generator';
    }

    public function getDescription()
    {
        return 'This generator generates angularjs files that implement CRUD (Create, Read, Update, Delete)
            operations for the specified data model.';
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return [];
    }

    /**
     * @return string the action view file path
     */
    public function getViewPath()
    {
        if ($this->viewPath !== null) {
            return \Yii::getAlias($this->viewPath) . '/' . Inflector::camel2id(
                get_class($this->getModel())
            ); //$this->getControllerID();
        } else {
            return parent::getViewPath();
        }

    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];

        // Workflow-related templates
        if (in_array(get_class($this->getModel()), array_keys(\ItemTypes::where('is_workflow_item')))) {

            // Edit workflow views
            foreach ($this->getModel()->flowSteps() as $step => $attributes) {
                $this->getModel()->scenario = "edit-step";
                $files[] = new CodeFile(
                    $this->jsTemplateDestination("steps/$step.html"),
                    $this->render('edit-step.html.php', compact("step", "attributes"))
                );
            }

            // Curate workflow views
            foreach ($this->getModel()->flowSteps() as $step => $attributes) {
                $this->getModel()->scenario = "curate-step";
                $files[] = new CodeFile(
                    $this->jsTemplateDestination("curate-steps/$step.html"),
                    $this->render('curate-step.html.php', compact("step", "attributes"))
                );
            }

            // Translate workflow views
            foreach ($this->getModel()->flowSteps() as $step => $attributes) {

                $translatableAttributes = $this->getModel()->matchingTranslatable($attributes);

                if (empty($translatableAttributes)) {
                    continue;
                }

                $this->getModel()->scenario = "translate-step";
                $files[] = new CodeFile(
                    $this->jsTemplateDestination("translate/steps/$step.html"),
                    $this->render('translate-step.html.php', compact("step", "translatableAttributes"))
                );
            }

        }

        /*
        // Other views
        $templatePath = $this->getTemplatePath() . '/views';
        foreach (scandir($templatePath) as $file) {
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile("$viewPath/$file", $this->render("views/$file"));
            }
        }
        */

        // Angularjs core
        $templatePath = $this->getTemplatePath() . '/core';
        foreach (scandir($templatePath) as $file) {
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile(
                    $this->jsTemplateDestination($file), $this->render("core/$file")
                );
            }
        }
        $templatePath = $this->getTemplatePath() . '/core/elements';
        foreach (scandir($templatePath) as $file) {
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile(
                    $this->jsTemplateDestination('elements/' . $file), $this->render("core/elements/$file")
                );
            }
        }

        return $files;
    }

    protected function jsTemplateDestination($file)
    {

        $viewPath = $this->getViewPath();
        $destination = str_replace(
            ".php",
            "",
            str_replace(
                "views/",
                "crud/",
                "$viewPath/$file"
            )
        );
        return $destination;

    }

    public function hasOneRelatedModelClasses()
    {
        $model = $this->getModel();
        $relations = $model->relations();
        $return = [];
        foreach ($model->itemTypeAttributes() as $attribute => $attributeInfo) {
            if ($attributeInfo["type"] == "has-one-relation") {
                if (!isset($relations[$attribute])) {
                    throw new \Exception("Model " . get_class($model) . " does not have a relation '$attribute'");
                }
                $relationInfo = $relations[$attribute];
                $relatedModelClass = $relationInfo[1];
                $return[] = $relatedModelClass;
            }
        }
        return array_unique($return);
    }

}
