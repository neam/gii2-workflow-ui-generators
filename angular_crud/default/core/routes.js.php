<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Html;

$model = $generator->getModel();

$modelClassSingular = get_class($model);
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);
$modelClassPluralId = Inflector::camel2id($modelClassPlural);
$labelSingular = ItemTypes::label($modelClassSingular, 1);
$labelPlural = ItemTypes::label($modelClassSingular, 2);
$labelNone = ItemTypes::label($modelClassSingular, 2);

// TODO: handle prefixes through config
$unprefixedModelClassSingular = str_replace(["Clerk", "Neamtime"], "", get_class($model));
$unprefixedModelClassSingularId = Inflector::camel2id($unprefixedModelClassSingular);
$unprefixedModelClassSingularWords = Inflector::camel2words($unprefixedModelClassSingular);
$unprefixedModelClassPluralWords = Inflector::pluralize($unprefixedModelClassSingularWords);
$unprefixedModelClassPlural = Inflector::camelize($unprefixedModelClassPluralWords);
$unprefixedModelClassPluralId = Inflector::camel2id($unprefixedModelClassPlural);

// TODO: fix choiceformat interpretation in yii2 and use item type choiceformat label for labels instead of inflector-created labels
$labelSingular = $unprefixedModelClassSingularWords;
$labelPlural = $unprefixedModelClassPluralWords;

?>
(function () {

    var module = angular.module('crud-<?= $modelClassSingularId ?>-routes', []);

    module.config(function ($stateProvider) {

        $stateProvider
            <?php
            if (in_array($modelClassSingular, array_keys(\ItemTypes::where('is_workflow_item')))):
                $stepCaptions = $model->flowStepCaptions();
                ?>

            .state('root.api-endpoints.existing.<?= $modelClassPluralId ?>', {
                abstract: true,
                url: "/<?= $unprefixedModelClassPluralId ?>",
                template: "<ui-view/>"
            })

            .state('root.api-endpoints.existing.<?= $modelClassPluralId ?>.list', {
                url: "/list",
                templateUrl: "crud/<?= $modelClassSingularId ?>/list.html",
                data: {pageTitle: 'List <?= $labelPlural ?>'}
            })

            .state('root.api-endpoints.existing.<?= $modelClassPluralId ?>.create', {
                url: "/new",
                templateUrl: "crud/<?= $modelClassSingularId ?>/form.html",
                data: {pageTitle: 'New <?= $labelSingular ?>'}
            })

            .state('root.api-endpoints.existing.<?= $modelClassPluralId ?>.existing', {
                abstract: true,
                url: "/:<?= lcfirst($modelClassSingular) ?>Id",
                controller: "edit<?= $modelClassSingular ?>Controller",
                template: "<ui-view/>"
            })

            /*
             .state('root.api-endpoints.existing.<?= $modelClassPluralId ?>.existing.dashboard', {
             url: "/dashboard",
             templateUrl: "crud/<?= $modelClassSingularId ?>/dashboard.html",
             data: {pageTitle: '<?= $labelPlural ?> Dashboard'}
             })
             */

            .state('root.api-endpoints.existing.<?= $modelClassPluralId ?>.existing.view', {
                url: "/view",
                templateUrl: "crud/<?= $modelClassSingularId ?>/view.html",
                data: {pageTitle: 'View <?= $labelSingular ?>'}
            })

            .state('root.api-endpoints.existing.<?= $modelClassPluralId ?>.existing.edit', {
                abstract: true,
                url: "/edit",
                resolve: {
                    setRouteBasedFilters: function (routeBasedFilters, $stateParams) {
                        // TODO: Generate the following dynamically based on data model
                        routeBasedFilters.Bar_order = 'Foo.id DESC';
                        routeBasedFilters.Bar_foo_id = $stateParams.fooId;
                    }
                },
                views: {
                    '': {
                        templateUrl: "crud/<?= $modelClassSingularId ?>/form.html"
                    },
                    'sidebar@root': {
                        templateUrl: "crud/<?= $modelClassSingularId ?>/navigation.html"
                    }
                },
                data: {showSideMenu: true}
            })

            <?php
            $flowSteps = $model->flowSteps();
            $flowStepReference = array_keys($flowSteps);
            $firstStepReference = reset($flowStepReference);
            ?>

            // Add initial alias for "first-step" TODO: Refactor to have dynamic step logic in angular logic
            .state('root.api-endpoints.existing.<?= $modelClassPluralId ?>.existing.edit.continue-editing', {
                url: "/continue-editing",
                templateUrl: "crud/<?= $modelClassSingularId ?>/steps/<?= $firstStepReference ?>.html",
                data: {pageTitle: 'Edit <?= $labelSingular ?>'}
            })

            <?php

            foreach ($flowSteps as $stepReference => $stepAttributes):

            // Determine level of step
            $stepHierarchy = explode(".", $stepReference);
            $step = end($stepHierarchy);

            switch (count($stepHierarchy)):

            case 1000: ?>

            <?php break; default: ?>

            // <?= json_encode(!empty($stepCaptions[$step]) ? $stepCaptions[$step] : ucfirst($step)) . "\n" ?>
            .state('root.api-endpoints.existing.<?= $modelClassPluralId ?>.existing.edit.<?= $stepReference ?>', {
                url: "/<?= $step ?>",
                templateUrl: "crud/<?= $modelClassSingularId ?>/steps/<?= $stepReference ?>.html",
                data: {pageTitle: 'Edit <?= $labelSingular ?>'}
            })

        <?php endswitch; ?>
        <?php endforeach; ?>

        <?php else: ?>

            // [not a workflow-item]

        <?php endif; ?>

        ;

    });

})();
