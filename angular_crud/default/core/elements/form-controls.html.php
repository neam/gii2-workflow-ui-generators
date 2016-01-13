<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = get_class($model);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);

?>
<button
        type="submit"
        class="btn btn-primary"
        ng-disabled="<?= lcfirst($modelClassSingular) ?>Form.$pristine || !<?= lcfirst($modelClassSingular) ?>Form.$valid || !<?= lcfirst($modelClassSingular) ?>.$resolved"
        >
    Save
</button>

<button
        type="button"
        class="btn btn-default"
        ng-disabled="<?= lcfirst($modelClassSingular) ?>Form.$pristine || !<?= lcfirst($modelClassSingular) ?>.$resolved"
        ng-click="reset(<?= lcfirst($modelClassSingular) ?>Form)"
        >Reset Form
</button>
