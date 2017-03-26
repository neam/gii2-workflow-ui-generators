<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = $generator->modelClass;
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);

?>
<button
        type="submit"
        class="btn btn-primary"
        ng-disabled="!$ctrl.form.$valid || !$ctrl.<?= lcfirst($modelClassSingular) ?>.$resolved"
        >
    <span ng-if="$ctrl.form.$pristine">Refresh</span><span ng-if="!$ctrl.form.$pristine">Save</span>
</button>

<button
        type="button"
        class="btn btn-default"
        ng-disabled="$ctrl.form.$pristine || !$ctrl.<?= lcfirst($modelClassSingular) ?>.$resolved"
        ng-click="reset($ctrl.form)"
        >Reset Form
</button>
