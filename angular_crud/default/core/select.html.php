<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Html;

$model = $generator->getModel();

$modelClassSingular = get_class($model);
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);
$modelClassPluralId = Inflector::camel2id($modelClassPlural);
$labelSingular = ItemTypes::label($modelClassSingular, 1);
$labelPlural = ItemTypes::label($modelClassSingular, 2);
$labelNone = ItemTypes::label($modelClassSingular, 2);

// TODO: handle prefixes through config
$unprefixedModelClassSingular = str_replace(["Clerk", "Neamtime"], "", get_class($model));
$unprefixedModelClassSingularId = Inflector::camel2id($unprefixedModelClassSingular);
$unprefixedModelClassSingularWords = Inflector::camel2words($unprefixedModelClassSingular);
$unprefixedModelClassPluralWords = Inflector::pluralize($unprefixedModelClassSingularWords);
$unprefixedModelClassPlural = Inflector::camelize($unprefixedModelClassPluralWords);
$unprefixedModelClassPluralId = Inflector::camel2id($unprefixedModelClassPlural);

// TODO: fix choiceformat interpretation in yii2 and use item type choiceformat label for labels instead of inflector-created labels
$labelSingular = ucfirst(strtolower($unprefixedModelClassSingularWords));
$labelPlural = ucfirst(strtolower($unprefixedModelClassPluralWords));

?>

<!--
<pre>{{ item | json }}</pre>
-->
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeIn list-<?= $modelClassPluralId ?>" ng-controller="list<?= $modelClassPlural ?>Controller">

            <div>

                <a href="javascript:void(0)" ng-click="selectItem(null)"
                   class="btn btn-sm btn-primary">Clear selection</a>

                <a href="javascript:void(0)" ng-click="<?= lcfirst($modelClassPlural) ?>.add()"
                   ng-show="<?= lcfirst($modelClassPlural) ?>.$resolved && <?= lcfirst($modelClassPlural) ?>.$promise.$$state.status === 1"
                   class="btn btn-sm btn-primary">Create new <?= lcfirst($labelSingular) ?></a>

                <div>

                    <div ng-include="'crud/<?= lcfirst($modelClassSingularId) ?>/elements/loading-status.html'"></div>

                    <div class="project-list" ng-show="<?= lcfirst($modelClassPlural) ?>.$resolved">

                        <table class="table table-hover">
                            <tbody>
                            <tr ng-repeat="<?= lcfirst($modelClassSingular) ?> in <?= lcfirst($modelClassPlural) ?>" ng-class="{selected: item.id === <?= lcfirst($modelClassSingular) ?>.id}">
                                <td class="project-label">
                                    <a href="javascript:void(0);" ng-click="selectItem(<?= lcfirst($modelClassSingular) ?>)">{{<?= lcfirst($modelClassSingular) ?>.item_label}}<span ng-if="item.id === <?= lcfirst($modelClassSingular) ?>.id"> SELECTED</span></a>
                                </td>
                                <td class="project-actions">
                                    <a href="javascript:void(0);" ng-click="selectItem(<?= lcfirst($modelClassSingular) ?>)" class="btn btn-white btn-sm"><i class="fa fa-crosshairs"></i> Select </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
