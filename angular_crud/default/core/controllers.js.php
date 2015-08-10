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

    module.controller('list<?= $modelClassPlural ?>Controller', function ($scope, <?= lcfirst($modelClassPlural) ?>, <?= lcfirst($modelClassSingular) ?>Resource, <?= lcfirst($modelClassSingular) ?>Crud) {

        $scope.<?= lcfirst($modelClassSingular) ?>Resource = <?= lcfirst($modelClassSingular) ?>Resource;
        $scope.<?= lcfirst($modelClassSingular) ?>Crud = <?= lcfirst($modelClassSingular) ?>Crud;
        $scope.<?= lcfirst($modelClassPlural) ?> = <?= lcfirst($modelClassPlural) ?>;

    });

    module.controller('edit<?= $modelClassSingular ?>Controller', function ($scope, $state, <?= lcfirst($modelClassPlural) ?>, <?= lcfirst($modelClassSingular) ?>Resource, <?= lcfirst($modelClassSingular) ?>Crud) {

        // Load existing or create new <?= $modelClassSingular ?> based on the existance of <?= lcfirst($modelClassSingular) ?>Id in the route
        console.log('edit<?= $modelClassPlural ?>Controller - $state.params', $state.params);
        var <?= lcfirst($modelClassSingular) ?>;
        if ($state.params.<?= lcfirst($modelClassSingular) ?>Id) {
            campaign = <?= lcfirst($modelClassSingular) ?>Resource.get({id: $state.params.<?= lcfirst($modelClassSingular) ?>Id});
        } else {
            campaign = new <?= lcfirst($modelClassSingular) ?>Resource(<?= lcfirst($modelClassSingular) ?>Resource.dataSchema);
        }

        // Form step visibility function
        $scope.showStep = function(step) {
            console.log('TODO: determine if step should be visible', step);
            return true;
        };

        // Form submit handling
        $scope.submit = function () {
            <?= lcfirst($modelClassSingular) ?>.$promise.then(function () {
                <?= lcfirst($modelClassSingular) ?>.$update(function (data) {
                    console.log('<?= $modelClassSingular ?> save success', data);
                }, function (error) {
                    console.log('<?= $modelClassSingular ?> save error', error);
                });
            });
        };

        $scope.<?= lcfirst($modelClassSingular) ?>Resource = <?= lcfirst($modelClassSingular) ?>Resource;
        $scope.<?= lcfirst($modelClassSingular) ?>Crud = <?= lcfirst($modelClassSingular) ?>Crud;
        $scope.<?= lcfirst($modelClassSingular) ?> = <?= lcfirst($modelClassSingular) ?>;

    });

})();