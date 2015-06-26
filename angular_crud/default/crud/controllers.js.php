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

    module.controller('list<?= $modelClassPlural ?>Controller', function ($scope, <?= lcfirst($modelClassPlural) ?>) {

        $scope.<?= lcfirst($modelClassPlural) ?> = [];

        <?= lcfirst($modelClassPlural)."\n" ?>
            .then(function (fetchedItems) {
                $scope.<?= lcfirst($modelClassPlural) ?> = fetchedItems;
            });

    });

    module.controller('curate<?= $modelClassPlural ?>Controller', function ($scope, <?= lcfirst($modelClassPlural) ?>) {

        $scope.<?= lcfirst($modelClassPlural) ?> = [];

        <?= lcfirst($modelClassPlural)."\n" ?>
            .then(function (fetchedItems) {
                $scope.<?= lcfirst($modelClassPlural) ?> = fetchedItems;
            });

    });

})();