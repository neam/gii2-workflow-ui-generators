'use strict';

let module = /*@ngInject*/ function ($scope,
                                     $state,
                                     $location,
                                     clerkLedgerEntries,
                                     restrictUi) {

    // To be able to read filter parameters in views
    $scope.$state = $state;

    // For restrictUi to be available in components using this as their controller
    $scope.restrictUi = restrictUi;

    // Make filtered collection available to scope
    $scope.clerkLedgerEntries = clerkLedgerEntries;

    // Filters
    $scope.cf_ClerkLedgerEntry_search = $location.search()['cf_ClerkLedgerEntry_search'] || '';
    $scope.cf_ClerkLedgerAccount_search = $location.search()['cf_ClerkLedgerAccount_search'] || '';
    $scope.cf_ClerkLedgerEntry_ClerkLedgerEntryRow_clerk_ledger_account_id = $location.search()['cf_ClerkLedgerEntry_ClerkLedgerEntryRow_clerk_ledger_account_id'] || '';
    $scope.cf_ClerkLedgerEntry_relevant_clerk_invoice_id = parseInt($location.search()['cf_ClerkLedgerEntry_relevant_clerk_invoice_id']);
    $scope.cf_ClerkLedgerEntry_relevant_clerk_travel_claim_id = parseInt($location.search()['cf_ClerkLedgerEntry_relevant_clerk_travel_claim_id']);
    $scope.cf_ClerkLedgerEntry_supporting_documents_deemed_good_enough = parseInt($location.search()['cf_ClerkLedgerEntry_supporting_documents_deemed_good_enough']);

    /*
     $scope.cf_ClerkLedgerEntry_min_impact = $location.search()['cf_ClerkLedgerEntry_min_impact'] || '';
     $scope.cf_ClerkLedgerEntry_max_impact = $location.search()['cf_ClerkLedgerEntry_max_impact'] || '';
     */

    // UI-only filters
    $scope.uif_ClerkLedgerEntry_min_impact = $location.search()['uif_ClerkLedgerEntry_min_impact'] || '';
    $scope.uif_ClerkLedgerEntry_max_impact = $location.search()['uif_ClerkLedgerEntry_max_impact'] || '';

};

export default module;
