<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = $generator->modelClass;
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassSingularIdUnderscored = Inflector::camel2id($modelClassSingular, "_");
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);

?>
'use strict';

let module = angular
    .module('crud-<?= Inflector::camel2id($modelClassSingular) ?>-controllers', [])

    .controller('list<?= $modelClassPlural ?>Controller', function (
        $scope,
        $location,
        restrictUi,
        visibilitySettings,
        <?= lcfirst($modelClassPlural) ?>,
        <?= lcfirst($modelClassSingular) ?>Resource,
        <?= lcfirst($modelClassSingular) ?>Crud
    ) {

        // For restrictUi to be available in components using this as their controller
        $scope.restrictUi = restrictUi;

        // Activate collections used in view
        <?= lcfirst($modelClassPlural) ?>.$activate();

        // Make collections available to scope
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
            /*
            rowHeaders: function(index) {
              return '';
            },
            */
            colHeaders: true,
            contextMenu: false, // ['row_above', 'row_below', 'remove_row'],
            persistentState: false,
            minSpareRows: 0,
            manualRowMove: true,
            manualColumnMove: true,
            fixedColumnsLeft: 3,
            fixedRowsTop: 0,
            autoRowSize: true,
            autoColumnSize: true,
            colWidths: [21, 23, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150],
            renderAllRows: true, // necessary so that handsontable does not think it is enough to render one or two rows after changing page / filter
            manualRowResize: true,
            manualColumnResize: true,
            formulas: false, // false since formula support is experimental and we want less bugs by default
            comments: true,
            dataSchema: <?= lcfirst($modelClassSingular) ?>Resource.dataSchema()
        };

<?php
$workflowItem = in_array($modelClassSingular, array_keys(\ItemTypes::where('is_workflow_item')));
if ($workflowItem):
?>
        // Decide which columns to display
        let visibleColumns = function () {

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

    })

    .controller('edit<?= $modelClassSingular ?>Controller', function ($scope, <?= lcfirst($modelClassSingular) ?>, edit<?= $modelClassSingular ?>ControllerService) {

        edit<?= $modelClassSingular ?>ControllerService.loadIntoScope($scope, <?= lcfirst($modelClassSingular) ?>);

    })

    .service('edit<?= $modelClassSingular ?>ControllerService', function ($state, $rootScope, <?= lcfirst($modelClassSingular) ?>Resource, <?= lcfirst($modelClassSingular) ?>Crud) {

        return {
            loadIntoScope: function ($scope, <?= lcfirst($modelClassSingular) ?>) {

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
                    return <?= lcfirst($modelClassSingular) ?>.refresh();
                };
                $scope.persistModel = function (form) {
                    <?= lcfirst($modelClassSingular) ?>.$promise.then(function () {
                        <?= lcfirst($modelClassSingular) ?>.$update(function (updatedItem) {
                            console.log('<?= $modelClassSingular ?> save success', updatedItem);
                            if (form) {
                                form.$setPristine();
                                form.$setUntouched();
                            }
                            // Tell collection to use the updated item attributes
                            clerkLedgerDeliverables.shouldUseThisUpdatedItemIfExistsInCollection(updatedItem);
                        }, function (error) {
                            console.log('<?= $modelClassSingular ?> save error', error);
                        });
                    });
                };

                $scope.<?= lcfirst($modelClassSingular) ?>Resource = <?= lcfirst($modelClassSingular) ?>Resource;
                $scope.<?= lcfirst($modelClassSingular) ?>Crud = <?= lcfirst($modelClassSingular) ?>Crud;
                $scope.<?= lcfirst($modelClassSingular) ?> = <?= lcfirst($modelClassSingular) ?>;

                // Reset form function
                $scope.reset = function (form) {
                    $scope.refreshModel().then(function() {
                        if (form) {
                            form.$setPristine();
                            form.$setUntouched();
                        }
                    });
                };

                // Share scope on rootScope so that side-menu can access it easily
                $rootScope.edit<?= $modelClassSingular ?>Controller = {};
                $rootScope.edit<?= $modelClassSingular ?>Controller.$scope = $scope;

            }
        };

    })

    .controller('current<?= $modelClassSingular ?>ClassificationController', function ($scope, $timeout, edit<?= $modelClassSingular ?>ControllerService, <?= lcfirst($modelClassPlural) ?>) {

        <?= lcfirst($modelClassPlural) ?>.currentItemInFocus = null;
        $scope.current<?= $modelClassSingular ?> = null;

        // Next/Prev button logic
        var currentIndex = function () {
            var item = _.find(<?= lcfirst($modelClassPlural) ?>, function (item) {
                return item.id == <?= lcfirst($modelClassPlural) ?>.currentItemInFocus.id;
            });
            return _.indexOf(<?= lcfirst($modelClassPlural) ?>, item);
        };

        var previousCtr = function () {
            return <?= lcfirst($modelClassPlural) ?>[currentIndex() - 1];
        };

        var nextCtr = function () {
            return <?= lcfirst($modelClassPlural) ?>[currentIndex() + 1];
        };

        $scope.currentIndex = currentIndex;

        $scope.previous = function () {
            <?= lcfirst($modelClassPlural) ?>.setCurrentItemInFocus(previousCtr());
        };

        $scope.next = function () {
            <?= lcfirst($modelClassPlural) ?>.setCurrentItemInFocus(nextCtr());
        };

        $scope.$watch(function () {
            //console.log('current<?= $modelClassSingular ?>ClassificationController watch check - <?= lcfirst($modelClassPlural) ?>.currentItemInFocus', <?= lcfirst($modelClassPlural) ?>.currentItemInFocus);
            return <?= lcfirst($modelClassPlural) ?>.currentItemInFocus;
        }, function (newVal, oldVal) {
            if (newVal && newVal.item_type === '<?= $modelClassSingularIdUnderscored ?>') {

                $scope.current<?= $modelClassSingular ?> = newVal;
                $scope.current<?= $modelClassSingular ?>.$promise = <?= lcfirst($modelClassPlural) ?>.$promise;
                $scope.current<?= $modelClassSingular ?>.$resolved = <?= lcfirst($modelClassPlural) ?>.$resolved;
                //console.log('current<?= $modelClassSingular ?>ClassificationController watch', $scope.current<?= $modelClassSingular ?>);

            edit<?= $modelClassSingular ?>ControllerService.loadIntoScope($scope, $scope.current<?= $modelClassSingular ?>);

            // Animate
            /*
             $('#current-classification-form').removeAttr('class').attr('class', '');

             $timeout(function () {
             var animation = 'bounce';
             $('#current-classification-form').addClass('animated');
             $('#current-classification-form').addClass(animation);
             });
             */

            }
        });

    });

export default module;
