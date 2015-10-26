<?php

namespace neam\gii2_workflow_ui_generators\yii1_crud;

use Yii;
use yii\gii\CodeFile;
use neam\gii2_workflow_ui_generators\yii1_crud\providers\CallbackProvider;
//use neam\gii2_workflow_ui_generators\yii1_crud\providers\DateTimeProvider;
//use neam\gii2_workflow_ui_generators\yii1_crud\providers\EditorProvider;
//use neam\gii2_workflow_ui_generators\yii1_crud\providers\RelationProvider;
use yii\helpers\Json;

/**
 * Yii Workflow UI Generator.
 * @author Fredrik Wollsén <fredrik@neam.se>
 * @since 1.0
 */
class Generator extends \schmunk42\giiant\crud\Generator
{

    public $baseControllerClass = 'Controller';

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Workflow UI CRUD Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates A) a yii-workflow-ui compatible controller + views and B) Angular Frontend Views that implement CRUD (Create, Read, Update, Delete)
            operations for the specified data model';
    }

    static public function getCoreProviders()
    {
        return [
            CallbackProvider::className(),
            //DateTimeProvider::className(),
            //EditorProvider::className(),
            //RelationProvider::className(),
        ];
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

    /**
     * Generates code for active field by using the provider queue
     *
     * @param string $attribute
     * @param null $model
     *
     * @return mixed|string
     */
    public function activeFieldForAttribute($attribute, $model = null, $params = [])
    {
        Yii::trace("Rendering activeField for '{$attribute}'", __METHOD__);
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $params);
        if ($code !== null) {
            return $code;
        } else {
            throw new \CException("This generator requires a non-null result from providers for each attribute. Attribute '{$attribute}' returned null");
        };
    }

    public function prependActiveFieldForAttribute($attribute, $model = null, $params = [])
    {
        Yii::trace("Rendering activeField for '{$attribute}'", __METHOD__);
        if ($model === null) {
            $model = $this->modelClass;
        }
        return $this->callProviderQueue(__FUNCTION__, $attribute, $model, $params);
    }

    public function appendActiveFieldForAttribute($attribute, $model = null, $params = [])
    {
        Yii::trace("Rendering activeField for '{$attribute}'", __METHOD__);
        if ($model === null) {
            $model = $this->modelClass;
        }
        return $this->callProviderQueue(__FUNCTION__, $attribute, $model, $params);
    }

    public function columnFormatForAttribute($attribute, $model = null, $params = [])
    {
        Yii::trace("Rendering columnFormat for '{$attribute}'", __METHOD__);
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $params);
        if ($code !== null) {
            return $code;
        } else {
            throw new \CException("This generator requires a non-null result from each attribute. Attribute '{$attribute}' returned null");
        };
    }

    public function attributeFormatForAttribute($attribute, $model = null, $params = [])
    {
        Yii::trace("Rendering attributeFormat for '{$attribute}'", __METHOD__);
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $params);
        if ($code !== null) {
            return $code;
        }
        throw new \CException("This generator requires a non-null result from each attribute. Attribute '{$attribute}' returned null");
    }

    public function relationGrid($name, $relation, $showAllRecords = false)
    {
        Yii::trace("Rendering relationGrid", __METHOD__);
        return $this->callProviderQueue(__FUNCTION__, $name, $relation, $showAllRecords);
    }

    /**
     * Clone of parent's _p because it is declared private
     * @var array
     */
    private $_p = [];

    /**
     * Clone of parent's callProviderQueue because it is declared private
     * @param $func
     * @param $args
     * @return mixed
     */
    private function callProviderQueue($func, $args)
    {
        $this->initializeProviders(); // TODO: should be done on init, but providerList is empty
        //var_dump($this->_p);exit;
        $args = func_get_args();
        unset($args[0]);
        // walk through providers
        foreach ($this->_p AS $obj) {
            if (method_exists($obj, $func)) {
                $c = call_user_func_array(array(&$obj, $func), $args);
                // until a provider returns not null
                if ($c !== null) {
                    if (is_object($args)) {
                        $argsString = get_class($args);
                    } elseif (is_array($args)) {
                        $argsString = Json::encode($args);
                    } else {
                        $argsString = $args;
                    }
                    $msg = 'Using provider ' . get_class($obj) . '::' . $func . ' ' . $argsString;
                    Yii::trace($msg, __METHOD__);
                    return $c;
                }
            }
        }
    }

    /**
     * Clone of parent's initializeProviders because it is declared private
     */
    private function initializeProviders()
    {
        // TODO: this is a hotfix for an already initialized provider queue on action re-entry
        if ($this->_p !== []) {
            return;
        }
        if ($this->providerList) {
            foreach (explode(',', $this->providerList) AS $class) {
                $class = trim($class);
                if (!$class) {
                    continue;
                }
                $obj            = \Yii::createObject(['class' => $class]);
                $obj->generator = $this;
                $this->_p[]     = $obj;
                #\Yii::trace("Initialized provider '{$class}'", __METHOD__);
            }
        }
    }

}
