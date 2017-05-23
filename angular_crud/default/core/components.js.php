<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Html;

$model = $generator->getModel();

$modelClass = $generator->modelClass;
$modelClassSingular = $modelClass;
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);
$modelClassPluralId = Inflector::camel2id($modelClassPlural);
$labelSingular = ItemTypes::label($modelClassSingular, 1);
$labelPlural = ItemTypes::label($modelClassSingular, 2);
$labelNone = ItemTypes::label($modelClassSingular, 2);

// TODO: handle prefixes through config
$unprefixedModelClassSingular = str_replace(["Clerk", "Neamtime"], "", $modelClass);
$unprefixedModelClassSingularId = Inflector::camel2id($unprefixedModelClassSingular);
$unprefixedModelClassSingularWords = Inflector::camel2words($unprefixedModelClassSingular);
$unprefixedModelClassPluralWords = Inflector::pluralize($unprefixedModelClassSingularWords);
$unprefixedModelClassPlural = Inflector::camelize($unprefixedModelClassPluralWords);
$unprefixedModelClassPluralId = Inflector::camel2id($unprefixedModelClassPlural);

// TODO: fix choiceformat interpretation in yii2 and use item type choiceformat label for labels instead of inflector-created labels
$labelSingular = $unprefixedModelClassSingularWords;
$labelPlural = $unprefixedModelClassPluralWords;

?>
'use strict';

var module = angular.module('crud-<?= $modelClassSingularId ?>-components', [
    require('./services.js').default.name,
    require('./controllers.js').default.name,
]);

module
    .component('crud<?= $modelClassSingular ?>Count', {
        template: require('./components/count.html'),
        controller: 'list<?= $modelClassPlural ?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>Filters', {
        bindings: {
            'renderInMenu': '<',
        },
        template: require('./components/filters.html'),
        controller: 'filter<?= $modelClassPlural ?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>FiltersToggleAndStatus', {
        template: require('./components/filters-toggle-and-status.html'),
        controller: 'filter<?= $modelClassPlural ?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>Sortings', {
        bindings: {
            'renderInMenu': '<',
        },
        template: require('./components/sortings.html'),
        controller: 'sorting<?= $modelClassPlural ?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>SortingsToggleAndStatus', {
        template: require('./components/sortings-toggle-and-status.html'),
        controller: 'filter<?= $modelClassPlural ?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>Groupings', {
        bindings: {
            'renderInMenu': '<',
        },
        template: require('./components/groupings.html'),
        controller: 'filter<?= $modelClassPlural ?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>GroupingsToggleAndStatus', {
        template: require('./components/groupings-toggle-and-status.html'),
        controller: 'filter<?= $modelClassPlural ?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>ImportButton', {
        template: require('./components/import-button.html'),
        controller: function ($scope, $state, clerkLedgerDeliverable, restrictUi) {
            $scope.$state = $state;
            $scope.clerkLedgerDeliverable = clerkLedgerDeliverable;
            $scope.importRouteReference = '<?= $modelClassSingularId ?>';
            $scope.restrictUi = restrictUi;
        }
    })
    .component('crud<?= $modelClassSingular ?>Curate', {
        template: require('./components/curate.html'),
        controller: 'list<?=Inflector::pluralize($modelClass)?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>SideMenu', {
        template: require('./components/side-menu.html'),
        controller: 'list<?=Inflector::pluralize($modelClass)?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>CompactList', {
        template: require('./components/compact-list.html'),
        controller: 'list<?=Inflector::pluralize($modelClass)?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>CurrentClassification', {
        bindings: {
            'form': '=',
        },
        template: require('./components/current-classification.html'),
        controller: function ($scope, <?= lcfirst($modelClassSingular) ?>, restrictUi) {
            $scope.<?= lcfirst($modelClassSingular) ?> = <?= lcfirst($modelClassSingular) ?>;
            $scope.restrictUi = restrictUi;
        }
    })
    .component('crud<?= $modelClassSingular ?>CurrentClassificationForm', {
        template: require('./components/current-classification-form.html'),
        controller: function ($scope, <?= lcfirst($modelClassPlural) ?>) {
            $scope.<?= lcfirst($modelClassPlural) ?> = <?= lcfirst($modelClassPlural) ?>;
        }
    })
    .component('crud<?= $modelClassSingular ?>ElementsCurrentClassificationFormControls', {
        bindings: {
            'form': '=',
        },
        template: require('./components/current-classification-form-controls.html'),
        controller: function ($scope, <?= lcfirst($modelClassSingular) ?>, <?= lcfirst($modelClassPlural) ?>, edit<?= $modelClassSingular ?>ControllerService, restrictUi) {
            $scope.<?= lcfirst($modelClassSingular) ?> = <?= lcfirst($modelClassSingular) ?>;
            $scope.<?= lcfirst($modelClassPlural) ?> = <?= lcfirst($modelClassPlural) ?>;
            $scope.restrictUi = restrictUi;

            // Next/Prev button logic
            var currentIndex = function () {
                if (!<?= lcfirst($modelClassSingular) ?>) {
                    return -1;
                }
                var item = _.find(<?= lcfirst($modelClassPlural) ?>, function (item) {
                    return item.id == <?= lcfirst($modelClassSingular) ?>.id;
                });
                return _.indexOf(<?= lcfirst($modelClassPlural) ?>, item);
            };

            var previousCtr = function () {
                return <?= lcfirst($modelClassPlural) ?>[currentIndex() - 1];
            };

            var nextCtr = function () {
                return <?= lcfirst($modelClassPlural) ?>[currentIndex() + 1];
            };

            $scope.currentIndex = currentIndex;

            $scope.previous = function () {
                <?= lcfirst($modelClassPlural) ?>.setCurrentItemInFocus(previousCtr());
            };

            $scope.next = function () {
                <?= lcfirst($modelClassPlural) ?>.setCurrentItemInFocus(nextCtr());
            };

            edit<?= $modelClassSingular ?>ControllerService.loadIntoScope($scope, <?= lcfirst($modelClassSingular) ?>);

            // Reload item when id has changed so that the reset-functionality works as expected
            $scope.$watch('<?= lcfirst($modelClassSingular) ?>.id', function (newVal, oldVal) {
                edit<?= $modelClassSingular ?>ControllerService.loadIntoScope($scope, <?= lcfirst($modelClassSingular) ?>);
            });

        }
    })
    .component('crud<?= $modelClassSingular ?>Form', {
        template: require('./components/form.html'),
        controller: 'edit<?= $modelClassSingular ?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>FormTop', {
        template: require('./components/form.top.html'),
        bindings: {
            'form': '=',
        },
        controller: 'edit<?= $modelClassSingular ?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>ElementsFilterAsRelatedItem', {
        template: require('./components/filter-as-related-item.html'),
        bindings: {
            ngModel: '=',
            attributeRef: '<'
        },
        controller: function ($scope, $location, <?= lcfirst($modelClassPlural) ?>) {
            <?= lcfirst($modelClassPlural) ?>.$activate();
            $scope.$location = $location;
            $scope.<?= lcfirst($modelClassPlural) ?> = <?= lcfirst($modelClassPlural) ?>;
        },
    })
    .component('crud<?= $modelClassSingular ?>ElementsItemSelectionWidget', {
        template: require('./components/item-selection-widget.html'),
        bindings: {
            selectedItem: '=',
            ngModel: '=',
            attributeRef: '<'
        },
        controller: function ($scope, $location, GeneralModalControllerService, <?= lcfirst($modelClassPlural) ?>) {
            <?= lcfirst($modelClassPlural) ?>.$activate();
            $scope.$location = $location;
            $scope.<?= lcfirst($modelClassPlural) ?> = <?= lcfirst($modelClassPlural) ?>;
            $scope.openCurateModal = function (params) {
                let template = require('./components/curate-modal.html');
                let size = 'lg';
                GeneralModalControllerService.openWithinScope($scope, template, size, params);
            };
        },
    })
    .component('crud<?= $modelClassSingular ?>ElementsFormControls', {
        template: require('./components/form-controls.html'),
        bindings: {
            'form': '=',
        },
        controller: 'edit<?= $modelClassSingular ?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>ElementsLoadingStatus', {
        template: require('./components/loading-status.html'),
        controller: 'list<?=$modelClassPlural?>Controller'
    })
;

export default module;
