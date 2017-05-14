'use strict';

let module = /*@ngInject*/ function ($scope,
                                     $state,
                                     clerkLedgerEntries,
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
            /*
             iconClass: 'fa-sort-numeric-desc',
             reverseIconClass: 'fa-sort-numeric-asc',
             */
            iconClass: 'fa-sort-desc',
            reverseIconClass: 'fa-sort-asc',
            orderChunk: 'ClerkLedgerEntry.official_ordinal ASC',
            reverseOrderChunk: 'ClerkLedgerEntry.official_ordinal ASC',
        },
        {
            label: 'Amount',
            reverse: false,
            /*
             iconClass: 'fa-sort-amount-desc',
             reverseIconClass: 'fa-sort-amount-asc',
             */
            iconClass: 'fa-sort-desc',
            reverseIconClass: 'fa-sort-asc',
            orderChunk: 'ClerkLedgerEntry_ClerkLedgerEntryRow_sum_abs_amount DESC',
            reverseOrderChunk: 'ClerkLedgerEntry_ClerkLedgerEntryRow_sum_abs_amount DESC',
        },
        {
            label: 'Category',
            reverse: false,
            /*
             iconClass: 'fa-sort-alpha-asc',
             reverseIconClass: 'fa-sort-alpha-desc',
             */
            iconClass: 'fa-sort-asc',
            reverseIconClass: 'fa-sort-desc',
            orderChunk: 'ClerkLedgerEntry.clerk_ledger_entry_category_id IS NULL, ClerkLedgerEntry.clerk_ledger_entry_category_id ASC',
            reverseOrderChunk: 'ClerkLedgerEntry.clerk_ledger_entry_category_id IS NULL, ClerkLedgerEntry.clerk_ledger_entry_category_id DESC',
        },
    ];

    $scope.sortings = [];

    let syncSortingsUi = function (ClerkLedgerEntry_order) {

        // Make defaultSortings match ClerkLedgerEntry_order
        let stateParamSyncedSortings = {};
        _.each(defaultSortings, function (sorting, index, list) {
            let positionOfOrderInStateParam = ClerkLedgerEntry_order.indexOf(sorting.orderChunk);
            let positionOfReverseOrderInStateParam = ClerkLedgerEntry_order.indexOf(sorting.reverseOrderChunk);
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

    // Wait for ClerkLedgerEntry_order information to be available, then activate sortings ui
    let stopWatching = $scope.$watch(function () {
        return routeBasedContentFilters.ClerkLedgerEntry_order || $state.params.cf_ClerkLedgerEntry_order;
    }, function (ClerkLedgerEntry_order) {
        console.log('ClerkLedgerEntry_order available', ClerkLedgerEntry_order, typeof ClerkLedgerEntry_order);
        if (ClerkLedgerEntry_order) {
            syncSortingsUi(ClerkLedgerEntry_order);
        }
    });

    $scope.$watch('sortings', function (newSortings) {

        if (newSortings.length > 0) {

            let order = '';
            _.each(newSortings, function (sorting, index, list) {
                order = order + (!sorting.reverse ? sorting.orderChunk : sorting.reverseOrderChunk) + ', ';
            });
            order = order + 'ClerkLedgerEntry.id ASC';

            $timeout(function () {
                $state.go($state.current.name, {
                    'cf_ClerkLedgerEntry_order': order,
                });
            });

        }

    }, true);

};

export default module;
