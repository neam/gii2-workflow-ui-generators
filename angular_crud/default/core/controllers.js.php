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

    module.controller('list<?= $modelClassPlural ?>Controller', function ($scope, $location, <?= lcfirst($modelClassPlural) ?>, <?= lcfirst($modelClassSingular) ?>Resource, <?= lcfirst($modelClassSingular) ?>Crud) {

        $scope.<?= lcfirst($modelClassSingular) ?>Resource = <?= lcfirst($modelClassSingular) ?>Resource;
        $scope.<?= lcfirst($modelClassSingular) ?>Crud = <?= lcfirst($modelClassSingular) ?>Crud;
        $scope.<?= lcfirst($modelClassPlural) ?> = <?= lcfirst($modelClassPlural) ?>;

        // Tmp workaround for the fact that <?= lcfirst($modelClassPlural) ?>.$metadata is not watchable (no change is detected, even on equality watch) from the controller scope for whatever reason
        $scope.$on('<?= $modelClassSingular ?>_metadataUpdated', function (ev, metadata) {
            angular.extend(<?= lcfirst($modelClassPlural) ?>.$metadata, metadata);
        });

        // Listen to page changes in pagination controls
        $scope.pageChanged = function () {
            console.log('Page changed to: ' + $scope.<?= lcfirst($modelClassPlural) ?>.$metadata.currentPage);
            $location.search('<?= $modelClassSingular ?>_page', $scope.<?= lcfirst($modelClassPlural) ?>.$metadata.currentPage);
        };

        // Prepare handsontable configuration with default callbacks
        $scope.handsontableSettings = {
            onAfterSelectionEndByProp: <?= lcfirst($modelClassSingular) ?>Crud.handsontable.afterSelectionEndByPropCallback,
            onAfterChange: <?= lcfirst($modelClassSingular) ?>Crud.handsontable.afterChange
        };

    });

    module.controller('edit<?= $modelClassSingular ?>Controller', function ($scope, $state, $rootScope, <?= lcfirst($modelClassSingular) ?>, <?= lcfirst($modelClassSingular) ?>Resource, <?= lcfirst($modelClassSingular) ?>Crud) {

        // Form step visibility function
        $scope.showStep = function (step) {
            console.log('TODO: determine if step should be visible', step);
            return true;
        };

        // Form submit handling
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