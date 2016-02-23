<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = get_class($model);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);

?>
(function () {

    var module = angular.module('crud-<?= Inflector::camel2id($modelClassSingular) ?>-controllers', []);

    module.controller('list<?= $modelClassPlural ?>Controller', function ($scope, $location, visibilitySettings, <?= lcfirst($modelClassPlural) ?>, <?= lcfirst($modelClassSingular) ?>Resource, <?= lcfirst($modelClassSingular) ?>Crud) {

        $scope.<?= lcfirst($modelClassSingular) ?>Resource = <?= lcfirst($modelClassSingular) ?>Resource;
        $scope.<?= lcfirst($modelClassSingular) ?>Crud = <?= lcfirst($modelClassSingular) ?>Crud;
        $scope.<?= lcfirst($modelClassPlural) ?> = <?= lcfirst($modelClassPlural) ?>;

        // Listen to page changes in pagination controls
        $scope.pageChanged = function () {
            console.log('Page changed to: ' + $scope.<?= lcfirst($modelClassPlural) ?>.$metadata.currentPage);
            $location.search('cf_<?= $modelClassSingular ?>_page', $scope.<?= lcfirst($modelClassPlural) ?>.$metadata.currentPage);
        };

        // Handsontable base configuration
        $scope.handsontableSettings = {
            afterSelectionEndByProp: <?= lcfirst($modelClassSingular) ?>Crud.handsontable.afterSelectionEndByPropCallback,
            afterChange: <?= lcfirst($modelClassSingular) ?>Crud.handsontable.afterChange,
            currentRowClassName: 'current-row',
            currentColClassName: 'current-column',
            rowHeaders: false,
            colHeaders: true,
            contextMenu: false, // ['row_above', 'row_below', 'remove_row'],
            persistentState: true,
            minSpareRows: 0,
            manualRowMove: true,
            manualColumnMove: true,
            fixedColumnsLeft: 0,
            manualRowResize: true,
            manualColumnResize: true,
            formulas: true,
            comments: true,
            dataSchema: <?= lcfirst($modelClassSingular) ?>Resource.dataSchema()
        };

<?php
$workflowItem = in_array($modelClassSingular, array_keys(\ItemTypes::where('is_workflow_item')));
if ($workflowItem):
?>
        // Decide which columns to display
        visibleColumns = function () {

            var vs = visibilitySettings.itemTypeSpecific('<?= $modelClassSingular ?>');

            if (vs.<?= $modelClassSingular ?>_columns_by_step) {
                return <?= lcfirst($modelClassSingular) ?>Crud.handsontable.workflowColumns[vs.<?= $modelClassSingular ?>_columns_by_step];
            }

            // TODO: consider _hide_source_relation

            return <?= lcfirst($modelClassSingular) ?>Crud.handsontable.crudColumns;

        };
        $scope.handsontableSettings.columns = visibleColumns();
<?php else: ?>
        $scope.handsontableSettings.columns = <?= lcfirst($modelClassSingular) ?>Crud.handsontable.crudColumns;
<?php endif; ?>

    });

    module.controller('edit<?= $modelClassSingular ?>Controller', function ($scope, $state, $rootScope, <?= lcfirst($modelClassSingular) ?>, <?= lcfirst($modelClassSingular) ?>Resource, <?= lcfirst($modelClassSingular) ?>Crud) {

        // Form step visibility function
        $scope.showStep = function (step) {
            console.log('TODO: determine if step should be visible', step);
            return true;
        };

        // Form submit handling
        $scope.saveOrRefresh = function (form) {
            if (form.$pristine) {
                $scope.refreshModel();
            } else {
                $scope.persistModel(form);
            }
        };
        $scope.refreshModel = function () {
            <?= lcfirst($modelClassSingular) ?>.$promise.then(function () {
                <?= lcfirst($modelClassSingular) ?>.$get(function (data) {
                    console.log('<?= $modelClassSingular ?> refresh success', data);
                }, function (error) {
                    console.log('<?= $modelClassSingular ?> refresh error', error);
                });
            });
        };
        $scope.persistModel = function (form) {
            <?= lcfirst($modelClassSingular) ?>.$promise.then(function () {
                <?= lcfirst($modelClassSingular) ?>.$update(function (data) {
                    console.log('<?= $modelClassSingular ?> save success', data);
                    if (form) {
                        form.$setPristine();
                        form.$setUntouched();
                    }
                }, function (error) {
                    console.log('<?= $modelClassSingular ?> save error', error);
                });
            });
        };

        $scope.<?= lcfirst($modelClassSingular) ?>Resource = <?= lcfirst($modelClassSingular) ?>Resource;
        $scope.<?= lcfirst($modelClassSingular) ?>Crud = <?= lcfirst($modelClassSingular) ?>Crud;
        $scope.<?= lcfirst($modelClassSingular) ?> = <?= lcfirst($modelClassSingular) ?>;

        // Save a original copy of the item so that we can reset the form
        $scope.original<?= $modelClassSingular ?> = {};
        <?= lcfirst($modelClassSingular) ?>.$promise.then(function () {
            $scope.original<?= $modelClassSingular ?> = angular.copy(<?= lcfirst($modelClassSingular) ?>);
        });

        // Reset form function
        $scope.reset = function (form) {
            if (form) {
                form.$setPristine();
                form.$setUntouched();
            }
            $scope.<?= lcfirst($modelClassSingular) ?> = angular.copy($scope.original<?= $modelClassSingular ?>);
        };

        // Share scope on rootScope so that side-menu can access it easily
        $rootScope.edit<?= $modelClassSingular ?>Controller = {};
        $rootScope.edit<?= $modelClassSingular ?>Controller.$scope = $scope;

    });

})();
