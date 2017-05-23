<?php

use yii\helpers\Inflector;

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

let module = /*@ngInject*/ function ($scope,
                                     $state,
                                     <?= lcfirst($modelClassPlural) ?>,
                                     routeBasedContentFilters,
                                     $timeout,
                                     restrictUi) {

    // To be able to read filter parameters in views
    $scope.$state = $state;

    // For restrictUi to be available in components using this as their controller
    $scope.restrictUi = restrictUi;

    // The sortings list
    let defaultSortings = [
        {
            label: 'Ordinal',
            reverse: false,
            iconClass: 'fa-sort-desc',
            reverseIconClass: 'fa-sort-asc',
            orderChunk: '<?= $modelClassSingular ?>.ordinal ASC',
            reverseOrderChunk: '<?= $modelClassSingular ?>.ordinal ASC',
        },
        {
            label: 'Amount',
            reverse: false,
            iconClass: 'fa-sort-desc',
            reverseIconClass: 'fa-sort-asc',
            orderChunk: '<?= $modelClassSingular ?>_amount DESC',
            reverseOrderChunk: '<?= $modelClassSingular ?>_amount DESC',
        },
        {
            label: 'Foo',
            reverse: false,
            iconClass: 'fa-sort-asc',
            reverseIconClass: 'fa-sort-desc',
            orderChunk: '<?= $modelClassSingular ?>.foo_id IS NULL, <?= $modelClassSingular ?>.foo_id ASC',
            reverseOrderChunk: '<?= $modelClassSingular ?>.foo_id IS NULL, <?= $modelClassSingular ?>.foo_id DESC',
        },
    ];

    $scope.sortings = [];

    let syncSortingsUi = function (<?= $modelClassSingular ?>_order) {

        // Make defaultSortings match <?= $modelClassSingular ?>_order
        let stateParamSyncedSortings = {};
        _.each(defaultSortings, function (sorting, index, list) {
            let positionOfOrderInStateParam = <?= $modelClassSingular ?>_order.indexOf(sorting.orderChunk);
            let positionOfReverseOrderInStateParam = <?= $modelClassSingular ?>_order.indexOf(sorting.reverseOrderChunk);
            if (positionOfOrderInStateParam > -1) {
                stateParamSyncedSortings[positionOfOrderInStateParam] = sorting;
            } else if (positionOfReverseOrderInStateParam > -1) {
                sorting.reverse = true;
                stateParamSyncedSortings[positionOfReverseOrderInStateParam] = sorting;
            } else {
                // Include the sorting even though it was not present, but do it at the lowest priority
                stateParamSyncedSortings[1000 + index] = sorting;
            }
        });
        $scope.stateParamSyncedSortings = stateParamSyncedSortings;

        $scope.sortings = [];
        _.each(stateParamSyncedSortings, function (sorting) {
            $scope.sortings.push(sorting);
        });

        $scope.sortableOptions = {
            handle: '.sorting-handle'
        };

    };

    // Wait for <?= $modelClassSingular ?>_order information to be available, then activate sortings ui
    let stopWatching = $scope.$watch(function () {
        return routeBasedContentFilters.<?= $modelClassSingular ?>_order || $state.params.cf_<?= $modelClassSingular ?>_order;
    }, function (<?= $modelClassSingular ?>_order) {
        console.log('<?= $modelClassSingular ?>_order available', <?= $modelClassSingular ?>_order, typeof <?= $modelClassSingular ?>_order);
        if (<?= $modelClassSingular ?>_order) {
            syncSortingsUi(<?= $modelClassSingular ?>_order);
        }
    });

    $scope.$watch('sortings', function (newSortings) {

        if (newSortings.length > 0) {

            let order = '';
            _.each(newSortings, function (sorting, index, list) {
                order = order + (!sorting.reverse ? sorting.orderChunk : sorting.reverseOrderChunk) + ', ';
            });
            order = order + '<?= $modelClassSingular ?>.id ASC';

            $timeout(function () {
                $state.go($state.current.name, {
                    'cf_<?= $modelClassSingular ?>_order': order,
                });
            });

        }

    }, true);

};

export default module;
