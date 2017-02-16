<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Html;

$model = $generator->getModel();

$modelClassSingular = $generator->modelClass;
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);
$modelClassPluralId = Inflector::camel2id($modelClassPlural);
$labelSingular = ItemTypes::label($modelClassSingular, 1);
$labelPlural = ItemTypes::label($modelClassSingular, 2);
$labelNone = ItemTypes::label($modelClassSingular, 2);

// TODO: handle prefixes through config
$unprefixedModelClassSingular = str_replace(["Clerk", "Neamtime"], "", $generator->modelClass);
$unprefixedModelClassSingularId = Inflector::camel2id($unprefixedModelClassSingular);
$unprefixedModelClassSingularWords = Inflector::camel2words($unprefixedModelClassSingular);
$unprefixedModelClassPluralWords = Inflector::pluralize($unprefixedModelClassSingularWords);
$unprefixedModelClassPlural = Inflector::camelize($unprefixedModelClassPluralWords);
$unprefixedModelClassPluralId = Inflector::camel2id($unprefixedModelClassPlural);

// TODO: fix choiceformat interpretation in yii2 and use item type choiceformat label for labels instead of inflector-created labels
$labelSingular = ucfirst(strtolower($unprefixedModelClassSingularWords));
$labelPlural = ucfirst(strtolower($unprefixedModelClassPluralWords));

?>

<div class="row">
    <div class="col-lg-12">
        <div class="list-<?= $modelClassPluralId ?>" ng-controller="list<?= $modelClassPlural ?>Controller">

            <div>

                <a href="javascript:void(0)" ng-click="<?= lcfirst($modelClassPlural) ?>.add()"
                   ng-show="<?= lcfirst($modelClassPlural) ?>.$resolved && <?= lcfirst($modelClassPlural) ?>.$promise.$$state.status === 1"
                   class="btn btn-primary">Create new <?= lcfirst($labelSingular) ?></a>

                <div>

                    <crud-<?= lcfirst($modelClassSingularId) ?>-elements-loading-status></crud-<?= lcfirst($modelClassSingularId) ?>-elements-loading-status>

                    <div class="project-list" ng-show="<?= lcfirst($modelClassPlural) ?>.$resolved">

                        <table class="table table-hover">
                            <tbody>
                            <tr ng-repeat="<?= lcfirst($modelClassSingular) ?> in <?= lcfirst($modelClassPlural) ?>">
<?php /*
                                <td class="project-status">
                                    <span class="label label-warning">A label</span>
                                </td>
                                */ ?>
                                <td class="project-label">
                                    <a ui-sref="root.api-endpoints.existing.<?= $modelClassPluralId ?>.existing.edit.continue-editing({dataEnvironment: $root.activeDataEnvironment.slug, <?= lcfirst($modelClassSingular) ?>Id: <?= lcfirst($modelClassSingular) ?>.id})">{{<?= lcfirst($modelClassSingular) ?>.item_label}}</a>
<?php /*
                                    <br/>
                                    <small>{{<?= lcfirst($modelClassSingular) ?>.attributes.start}} - {{<?= lcfirst($modelClassSingular) ?>.attributes.stop}}</small>
                                    */ ?>
                                </td>
<?php /*
                                <td class="project-completion">
                                    <small>Completion with: X%</small>
                                    <div class="progress progress-mini">
                                        <div style="width: 48%;" class="progress-bar"></div>
                                    </div>
                                </td>
                                */ ?>
                                <td class="project-actions">
                                    <a ui-sref="root.api-endpoints.existing.<?= $modelClassPluralId ?>.existing.view({dataEnvironment: $root.activeDataEnvironment.slug, <?= lcfirst($modelClassSingular) ?>Id: <?= lcfirst($modelClassSingular) ?>.id})"
                                       href="#" class="btn btn-white btn-sm"><i class="fa fa-folder"></i> View </a>
                                    <a ui-sref="root.api-endpoints.existing.<?= $modelClassPluralId ?>.existing.edit.continue-editing({dataEnvironment: $root.activeDataEnvironment.slug, <?= lcfirst($modelClassSingular) ?>Id: <?= lcfirst($modelClassSingular) ?>.id})"
                                       href="#" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit </a>
                                    <a href="javascript:void(0);" ng-click="<?= lcfirst($modelClassPlural) ?>.remove(<?= lcfirst($modelClassSingular) ?>.id)" class="btn btn-white btn-sm"><i class="fa fa-trash-o"></i> Delete </a>
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

<div class="row" ng-if="restrictUi.byUserType(restrictUi.userTypes.ADVANCED)">
    <div class="col-lg-12">
        <div class="list-<?= $modelClassPluralId ?>" ng-controller="list<?= $modelClassPlural ?>Controller">

            <div class="ibox">
                <div class="ibox-title">
                    <h5>Excel-like quick edit</h5>

                    <div class="ibox-tools">
                        <a href="javascript:void(0)" ng-click="<?= lcfirst($modelClassPlural) ?>.add()" ng-show="<?= lcfirst($modelClassPlural) ?>.$promise.$$state.status === 1"
                           class="btn btn-primary btn-xs">Create new <?= lcfirst($labelSingular) ?></a>
                    </div>

                </div>
                <div class="ibox-content">

                    <crud-<?= lcfirst($modelClassSingularId) ?>-curate></crud-<?= lcfirst($modelClassSingularId) ?>-curate>

                </div>
            </div>
        </div>
    </div>
</div>
