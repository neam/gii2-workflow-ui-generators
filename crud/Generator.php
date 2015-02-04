<?php

namespace neam\yii_workflow_ui_giiant_generator\crud;

use Yii;
use yii\gii\CodeFile;

/**
 * Yii Workflow UI Generator.
 * @author Fredrik Wollsén <fredrik@neam.se>
 * @since 1.0
 */
class Generator extends \schmunk42\giiant\crud\Generator
{

    public $baseControllerClass = 'Controller';

    public function getName()
    {
        return 'Yii Workflow UI CRUD Generator';
    }

    public function getDescription()
    {
        return 'This generator generates a yii-workflow-ui compatible controller and views that implement CRUD (Create, Read, Update, Delete)
            operations for the specified data model.';
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];

        // Controller
        $controllerPath = $this->getControllerPath();
        $controllerFile = $controllerPath . str_replace('\\', '/', ltrim($this->controllerClass, '\\')) . '.php';
        $files[] = new CodeFile($controllerFile, $this->render('controller.php'));

        // View path
        $viewPath = $this->getViewPath();

        // Edit workflow views
        foreach ($this->getModel()->flowSteps() as $step => $attributes) {
            $stepViewPath = $viewPath . '/steps/' . $step . ".php";
            $files[] = new CodeFile($stepViewPath, $this->render('step.php', compact("step", "attributes")));
        }

        // Translate workflow views
        foreach ($this->getModel()->flowSteps() as $step => $attributes) {

            if (!$this->getModel()->anyTranslatable($attributes)) {
                continue;
            }

            $stepViewPath = $viewPath . '/translate/steps/' . $step . ".php";
            $files[] = new CodeFile($stepViewPath, $this->render('step.php', compact("step", "attributes")));
        }

        // Other views
        $templatePath = $this->getTemplatePath() . '/views';
        foreach (scandir($templatePath) as $file) {
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile("$viewPath/$file", $this->render("views/$file"));
            }
        }

        return $files;
    }

    public function getControllerPath()
    {
        return \Yii::getAlias(str_replace('views', 'controllers', $this->viewPath)) . '/';
    }

    /**
     * Alter validation rules to work with yii 1 templates
     * @return array
     */
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
     * An inline validator that checks if the attribute value refers to an existing class name.
     * If the `extends` option is specified, it will also check if the class is a child class
     * of the class represented by the `extends` option.
     * @param string $attribute the attribute being validated
     * @param array $params the validation options
     */
    public function validateClass($attribute, $params)
    {
        $class = $this->$attribute;
        try {
            if (class_exists($class)) {
                if (isset($params['extends'])) {
                    if (ltrim($class, '\\') !== ltrim($params['extends'], '\\') && !is_subclass_of($class, $params['extends'])) {
                        $this->addError($attribute, "'$class' must extend from {$params['extends']} or its child class.");
                    }
                }
            } else {
                $this->addError($attribute, "Class '$class' does not exist or has syntax error.");
            }
        } catch (\Exception $e) {
            //$this->addError($attribute, "Class '$class' does not exist or has syntax error.");
        }
    }

    /**
     * An inline validator that checks if the attribute value refers to a valid namespaced class name.
     * The validator will check if the directory containing the new class file exist or not.
     * @param string $attribute the attribute being validated
     * @param array $params the validation options
     */
    public function validateNewClass($attribute, $params)
    {
        $class = ltrim($this->$attribute, '\\');
        if (($pos = strrpos($class, '\\')) === false) {
            //$this->addError($attribute, "The class name must contain fully qualified namespace name.");
        } else {
            $ns = substr($class, 0, $pos);
            $path = Yii::getAlias('@' . str_replace('\\', '/', $ns), false);
            if ($path === false) {
                $this->addError($attribute, "The class namespace is invalid: $ns");
            } elseif (!is_dir($path)) {
                $this->addError($attribute, "Please make sure the directory containing this class exists: $path");
            }
        }
    }

    /**
     * Get model
     */
    public function getModel()
    {
        /* @var $class CActiveRecord */
        $class = $this->modelClass;
        return $class::model();
    }

    /**
     * @return string the yii 1 controller ID (without the module ID prefix)
     */
    public function getControllerID()
    {
        $pos = strrpos($this->controllerClass, '\\');
        $class = substr(ltrim($this->controllerClass, '\\'), 0, -10);

        return lcfirst($class);
    }

    /**
     * Checks if yii 1 model class is valid
     */
    public function validateModelClass()
    {
        /* @var $class CActiveRecord */
        $class = $this->modelClass;
        $table = $class::model()->getMetaData()->tableSchema;
        $pk = $table->primaryKey;
        if (empty($pk)) {
            $this->addError('modelClass', "The table associated with $class must have primary key(s).");
        }
    }

    /**
     * Returns table schema for current model class or false if it is not an active record
     * @return boolean|CDbTableSchema
     */
    public function getTableSchema()
    {
        /* @var $class CActiveRecord */
        $class = $this->modelClass;
        if (is_subclass_of($class, '\CActiveRecord')) {
            return $class::model()->getMetaData()->tableSchema;
        } else {
            return false;
        }
    }

    /**
     * @return array model column names
     */
    public function getColumnNames()
    {
        /* @var $class CActiveRecord */
        $class = $this->modelClass;
        if (is_subclass_of($class, '\CActiveRecord')) {
            return $class::model()->getMetaData()->tableSchema->getColumnNames();
        } else {
            /* @var $model \yii\base\Model */
            $model = new $class();

            return $model->attributes();
        }
    }

}
