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

    var module = angular.module('crud-<?= Inflector::camel2id($modelClassSingular) ?>-services', []);

    /**
     * Inject to get an object for querying, adding, removing items
     */
    module.service('<?= lcfirst($modelClassSingular) ?>Resource', function ($resource) {
        return $resource(env.API_BASE_URL + '/' + env.API_VERSION + '/<?= lcfirst($modelClassSingular) ?>/:id');
    });

    /**
     * Inject to get an actual populated modifiable array of items from database
     */
    module.service('<?= lcfirst($modelClassPlural) ?>', function (<?= lcfirst($modelClassSingular) ?>Resource) {
        return <?= lcfirst($modelClassSingular) ?>Resource.query().$promise;
    });

})();