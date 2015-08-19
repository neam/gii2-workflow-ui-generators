<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = get_class($model);
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);

// TODO: use item type choiceformat label for labels instead of inflector

?>
<form name="<?= lcfirst($modelClassSingular) ?>Form" ng-controller="edit<?= $modelClassSingular ?>Controller" ng-submit="submit()" novalidate>

    <div class="alert alert-info" ng-show="!<?= lcfirst($modelClassSingular) ?>.$resolved">
        Loading <?= lcfirst($modelClassSingular) ?>...
    </div>

    <div class="alert alert-warning"
         ng-show="<?= lcfirst($modelClassSingular) ?>.$promise.$$state.status === 1 && <?= lcfirst($modelClassSingular) ?>.length == 0">
        <?= $modelClassSingular ?> not found
    </div>

    <div class="alert alert-danger"
         ng-show="<?= lcfirst($modelClassSingular) ?>.$promise.$$state.status === 2">
        A problem was encountered when loading the <?= lcfirst($modelClassSingular) ?>. Please re-load the page.
    </div>

    <div ng-show="<?= lcfirst($modelClassSingular) ?>.$resolved && <?= lcfirst($modelClassSingular) ?>.$promise.$$state.status !== 2">

        <!-- Wrapper-->
        <div id="wrapper" class="new-<?= lcfirst($modelClassSingular) ?>">
            <!-- Navigation -->
            <nav class="navbar-default navbar-static-side" role="navigation">
                <div id="side-menu" class="nav" ng-include="'views/navigation/side-menu.html'"></div>
            </nav>
            <div class="animated fadeInUp wrapper-content">

                <div ng-include="'crud/<?= $modelClassSingularId ?>/form.top.html'"></div>
                <div ng-include="'crud/<?= $modelClassSingularId ?>/elements.html'"></div>

            </div>
        </div>
        <!-- End wrapper-->

    </div>

</form>