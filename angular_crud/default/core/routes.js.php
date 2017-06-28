<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Html;

$model = $generator->getModel();

$modelClass = $generator->modelClass;
$modelClassSingular = $modelClass;
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);
$modelClassPluralId = Inflector::camel2id($modelClassPlural);
$labelSingular = ItemTypes::label($modelClassSingular, 1);
$labelPlural = ItemTypes::label($modelClassSingular, 2);
$labelNone = ItemTypes::label($modelClassSingular, 2);

// TODO: handle prefixes through config
$unprefixedModelClassSingular = str_replace(["Clerk", "Neamtime"], "", $modelClass);
$unprefixedModelClassSingularId = Inflector::camel2id($unprefixedModelClassSingular);
$unprefixedModelClassSingularWords = Inflector::camel2words($unprefixedModelClassSingular);
$unprefixedModelClassPluralWords = Inflector::pluralize($unprefixedModelClassSingularWords);
$unprefixedModelClassPlural = Inflector::camelize($unprefixedModelClassPluralWords);
$unprefixedModelClassPluralId = Inflector::camel2id($unprefixedModelClassPlural);

// TODO: fix choiceformat interpretation in yii2 and use item type choiceformat label for labels instead of inflector-created labels
$labelSingular = $unprefixedModelClassSingularWords;
$labelPlural = $unprefixedModelClassPluralWords;

?>
'use strict';

let module = {};

(function (module) {

    var routes = function ($stateProvider, baseState) {

        $stateProvider

<?php

            $step = false;
            $stepReference = false;
            $parentState = '\' + baseState + \'';
            $rootCrudState = null; // The state where the views for list, view and edit etc are defined the first state - necessary to keep track of in order to be able to reference their ui-views
            $recursionLevel = 0;
            $params = compact("step", "stepReference", "parentState", "rootCrudState", "recursionLevel", "generator", "itemTypeAttributesWithAdditionalMetadata", "modelClass");
            $attribute = $modelClassPluralId;

            echo $generator->prependActiveFieldForAttribute("ui-router-item-type-states." . $attribute, $model, $params);
            echo $generator->activeFieldForAttribute("ui-router-item-type-states." . $attribute, $model, $params);
            echo $generator->appendActiveFieldForAttribute("ui-router-item-type-states." . $attribute, $model, $params);

?>


        ;

    };

    module.routes = routes;

    /*
     Usage example:
     someAngularModuleThatRequiresThisItemsCrudRouting.config(function ($stateProvider) {
         routes($stateProvider, 'root.api-endpoints.existing');
     });
     */

})(module);

export default module;
