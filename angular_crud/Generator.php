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

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'AngularJS Workflow UI CRUD Generator';
    }

    /**
     * @inheritdoc
     */
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

        $itemTypeAttributes = $this->getItemTypeAttributes($this->getModel());

        //if (get_class($this->getModel()) == "Campaign") {var_dump(__LINE__, $itemTypeAttributes);exit(1);}

        $generator = $this;

        // Workflow-related templates
        if (in_array(get_class($this->getModel()), array_keys(\ItemTypes::where('is_workflow_item')))) {

            // Edit workflow views
            foreach ($this->getModel()->flowSteps() as $step => $attributes) {
                $this->getModel()->scenario = "edit-step";
                $files[] = new CodeFile(
                    $this->jsTemplateDestination("steps/$step.html"),
                    $this->render(
                        'edit-step.html.php',
                        compact("step", "attributes", "itemTypeAttributes", "generator")
                    )
                );
            }

            // Curate workflow views
            foreach ($this->getModel()->flowSteps() as $step => $attributes) {
                $this->getModel()->scenario = "curate-step";
                $files[] = new CodeFile(
                    $this->jsTemplateDestination("curate-steps/$step.html"),
                    $this->render(
                        'curate-step.html.php',
                        compact("step", "attributes", "itemTypeAttributes", "generator")
                    )
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
                    $this->render(
                        'translate-step.html.php',
                        compact("step", "translatableAttributes", "itemTypeAttributes", "generator")
                    )
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
                    $this->jsTemplateDestination($file),
                    $this->render("core/$file", compact("itemTypeAttributes", "generator"))
                );
            }
        }
        $templatePath = $this->getTemplatePath() . '/core/elements';
        foreach (scandir($templatePath) as $file) {
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile(
                    $this->jsTemplateDestination('elements/' . $file),
                    $this->render("core/elements/$file", compact("itemTypeAttributes", "generator"))
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
        $return = [];
        foreach ($this->getItemTypeAttributes($model) as $attribute => $attributeInfo) {
            // Do not consider attributes referencing other item types
            if (strpos($attribute, '/') !== false) {
                continue;
            }
            if ($attributeInfo['type'] == 'has-one-relation') {
                $return[] = $attributeInfo['relatedModelClass'];
            }
        }
        return array_unique($return);
    }

    /**
     * Get item type attributes with additional metadata required during generation
     * TODO: Do not keep copy-pasted copies here and in yii1_rest_model/Generator
     */
    public function getItemTypeAttributes($model)
    {
        $modelClass = get_class($model);
        if (!method_exists($model, 'itemTypeAttributes')) {
            throw new \Exception("Model $modelClass does not have method itemTypeAttributes()");
        }
        $itemTypeAttributes = $model->itemTypeAttributes();
        foreach ($itemTypeAttributes as $attribute => &$attributeInfo) {

            // Do not decorate deep attributes with relation information yet - they are decorated on a needs basis further down
            if (strpos($attribute, '/') !== false) {
                continue;
            }

            // Decorate with relation information
            $this->decorateRelationInfo($modelClass, $attribute, $attributeInfo);

        }
        foreach ($itemTypeAttributes as $attribute => &$attributeInfo) {
            // Decorate with additional information about nested attributes
            if (strpos($attribute, '/') !== false) {
                $_ = explode('/', $attribute);
                $throughAttribute = $_[0];
                $deepAttribute = $_[1];
                // Nest deep attribute information
                $attributeInfo['throughAttribute'] = $itemTypeAttributes[$throughAttribute];
                $relatedModelClass = $attributeInfo['throughAttribute']['relatedModelClass'];
                $this->decorateRelationInfo($relatedModelClass, $deepAttribute, $attributeInfo);
                $itemTypeAttributes[$throughAttribute]['deepAttributes'][$deepAttribute] = $attributeInfo;
                continue;
            }
        }
        return $itemTypeAttributes;
    }

    public function decorateRelationInfo($modelClass, $attribute, &$attributeInfo)
    {

        $tableMapClass = "\\propel\\models\\Map\\{$modelClass}TableMap";
        if (!class_exists($tableMapClass)) {
            throw new \Exception(
                "Propel object model classes seem to be missing for model class $modelClass - specifically $tableMapClass does not exist"
            );
        }
        /** @var \Propel\Runtime\Map\TableMap $tableMap */
        $tableMap = $tableMapClass::getTableMap();

        try {

            $relations = [];

            switch ($attributeInfo['type']) {
                case "has-many-relation":
                case "many-many-relation":
                case "belongs-to-relation":

                    foreach ($tableMap->getRelations() as $relation) {
                        if ($relation->getType() === \Propel\Runtime\Map\RelationMap::ONE_TO_MANY) {
                            $relations[] = $relation->getName();
                        }
                    }

                    /** @var \Propel\Runtime\Map\RelationMap $relationInfo */
                    $relationInfo = null;
                    if (!empty($attributeInfo['db_column'])) {
                        // Method 1 - Use db_column information

                        if (strpos($attributeInfo['db_column'], ".") === false) {
                            throw new \Exception($attributeInfo['type']. " db_column needs to contain a dot that separates the related table with the relation attribute");
                        }

                        $_ = explode(".", $attributeInfo['db_column']);
                        $relatedTable = $_[0];
                        $relatedColumn = $_[1];

                        $relations = $tableMap->getRelations();
                        $relationInfo = null;
                        foreach ($relations as $candidateRelation) {
                            $columnMappings = $candidateRelation->getColumnMappings();
                            if (array_key_exists($attributeInfo['db_column'], $columnMappings)) {
                                $relationInfo = $candidateRelation;
                                break;
                            }
                        }
                    } else {
                        // Method 2 - Guess based on attribute name
                        $_ = explode("RelatedBy", $attribute);
                        $relatedModelClass = Inflector::singularize(ucfirst($_[0]));
                        if (in_array($relatedModelClass, $relations)) {
                            $relationName = $relatedModelClass;
                        } elseif (isset($_[1]) && in_array($relatedModelClass . "RelatedBy" . $_[1], $relations)) {
                            $relationName = $relatedModelClass . "RelatedBy" . $_[1];
                        } else {
                            $relationName = $attribute;
                        }
                        $relationInfo = $tableMap->getRelation($relationName);

                    }

                    $attributeInfo['relatedModelClass'] = $relationInfo->getForeignTable()->getPhpName();
                    $attributeInfo['relatedItemGetterMethod'] = "get" . $relationInfo->getName();
                    $attributeInfo['relatedItemSetterMethod'] = "set" . $relationInfo->getName();

                    break;
                case "has-one-relation":

                    foreach ($tableMap->getRelations() as $relation) {
                        if ($relation->getType() === \Propel\Runtime\Map\RelationMap::MANY_TO_ONE) {
                            $relations[] = $relation->getName();
                        }
                    }

                    /** @var \Propel\Runtime\Map\RelationMap $relationInfo */
                    $relationInfo = null;
                    if (!empty($attributeInfo['db_column'])) {
                        // Method 1 - Use db_column information
                        $column = $tableMap->getColumn($attributeInfo['db_column']);
                        $relationInfo = $column->getRelation();
                    } else {
                        // Method 2 - Guess based on attribute name
                        $relationName = ucfirst($attribute);
                        $relationInfo = $tableMap->getRelation($relationName);
                    }

                    /** @var \Propel\Runtime\Map\ColumnMap $localColumn */
                    $localColumns = $relationInfo->getLocalColumns();
                    $localColumn = array_shift($localColumns);
                    $attributeInfo['relatedModelClass'] = $relationInfo->getForeignTable()->getPhpName();
                    $attributeInfo['fkAttribute'] = $localColumn->getName();
                    $attributeInfo['relatedItemGetterMethod'] = "get" . $relationInfo->getName();
                    $attributeInfo['relatedItemSetterMethod'] = "set" . $relationInfo->getName();

                    break;
                case "ordinary":
                case "primary-key":
                    break;
                default:
                    // ignore
                    break;
            }

        } catch (\Propel\Runtime\Map\Exception\RelationNotFoundException $e) {
            throw new \Exception(
                "Could not find {$attributeInfo['type']} relation information for $modelClass->$attribute: " . $e->getMessage(
                ) . "\nAvailable relations for {$tableMap->getPhpName()}: \n - " . implode("\n - ", $relations)
                . (empty($attributeInfo['db_column']) ? "\n\nHint: By setting the db_column property in the item type attribute metadata, the relation information can be determined without guessing" : "")
            );
        } catch (\Propel\Runtime\Map\Exception\ColumnNotFoundException $e) {
            throw new \Exception(
                "Could not find {$attributeInfo['type']} relation information for $modelClass->$attribute due to a column not found exception: " . $e->getMessage(
                ) . "\nAvailable relations for {$tableMap->getPhpName()}: \n - " . implode("\n - ", $relations)
                . (empty($attributeInfo['db_column']) ? "\n\nHint: Make sure that the db_column property in the item type attribute metadata points to an existing column" : "")
            );
        }

    }

}
