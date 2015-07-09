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

        // View path
        $viewPath = $this->getViewPath();

        /*
        // Edit workflow views
        foreach ($this->getModel()->flowSteps() as $step => $attributes) {
            $stepViewPath = $viewPath . '/steps/' . $step . ".php";
            $this->getModel()->scenario = "edit-step";
            $files[] = new CodeFile($stepViewPath, $this->render('edit-step.php', compact("step", "attributes")));
        }

        // Translate workflow views
        foreach ($this->getModel()->flowSteps() as $step => $attributes) {

            $translatableAttributes = $this->getModel()->matchingTranslatable($attributes);

            if (empty($translatableAttributes)) {
                continue;
            }

            $stepViewPath = $viewPath . '/translate/steps/' . $step . ".php";
            $this->getModel()->scenario = "translate-step";
            $files[] = new CodeFile(
                $stepViewPath,
                $this->render('translate-step.php', compact("step", "translatableAttributes"))
            );
        }

        // Other views
        $templatePath = $this->getTemplatePath() . '/views';
        foreach (scandir($templatePath) as $file) {
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile("$viewPath/$file", $this->render("views/$file"));
            }
        }
        */

        // Angularjs core
        $templatePath = $this->getTemplatePath() . '/crud';
        foreach (scandir($templatePath) as $file) {
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {

                $destination = str_replace(
                        ".php",
                        "",
                        str_replace(
                            "views/",
                            "crud/",
                            "$viewPath/$file"
                        )
                    );
                $files[] = new CodeFile(
                    $destination, $this->render("crud/$file")
                );
            }
        }

        return $files;
    }

}
