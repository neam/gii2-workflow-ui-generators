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
<form name="<?= lcfirst($modelClassSingular) ?>Form" ng-submit="saveOrRefresh(campaignForm)" novalidate>

    <div class="wrapper-content" ng-show="!<?= lcfirst($modelClassSingular) ?>.$resolved">
        <i class="fa fa-circle-o-notch fa-spin fa-5x"></i>
    </div>

    <!--
    <div class="alert alert-info" ng-show="!<?= lcfirst($modelClassSingular) ?>.$resolved">
        Loading <?= lcfirst($modelClassSingular) ?>...
    </div>
    -->

    <div class="alert alert-warning"
         ng-show="<?= lcfirst($modelClassSingular) ?>.$promise.$$state.status === 1 && <?= lcfirst($modelClassSingular) ?>.length == 0">
        <?= $modelClassSingular ?> not found
    </div>

    <div class="alert alert-danger"
         ng-show="<?= lcfirst($modelClassSingular) ?>.$promise.$$state.status === 2">
        A problem was encountered when loading the <?= lcfirst($modelClassSingular) ?>. Please re-load the page.
    </div>

    <!-- By using ng-if instead of ng-show below, the form elements are not initialized until the item is fully loaded, something that helps certain UI elements (for instance select2) to initialize properly -->
    <div ng-if="<?= lcfirst($modelClassSingular) ?>.$resolved && <?= lcfirst($modelClassSingular) ?>.$promise.$$state.status !== 2">

        <!-- Wrapper-->
        <div id="wrapper" class="<?= $modelClassSingularId ?>">
            <div class="animated fadeIn wrapper-content">

                <div ng-include="'crud/<?= $modelClassSingularId ?>/form.top.html'"></div>
                <ui-view/>

            </div>
        </div>
        <!-- End wrapper-->

    </div>

</form>