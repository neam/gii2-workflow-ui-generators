<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = $modelClass = $generator->modelClass;
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$itemTypeSingularRef = Inflector::camel2id($modelClassSingular, '_');
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);

$workflowItem = in_array($modelClassSingular, array_keys(\ItemTypes::where('is_workflow_item')));

// http://www.yiiframework.com/doc-2.0/guide-rest-response-formatting.html
$itemsResponseKey = 'items';
$metadataResponseKey = '_meta';

?>
'use strict';

let dnaProjectBaseHandsontableCrudHelper = require('components/dna-project-base-handsontable/crudHelper');

let module = angular
    .module('crud-<?= Inflector::camel2id($modelClassSingular) ?>-services', [])

    /**
     * Inject to get an object for querying, adding, removing items
     */
    .service('<?= lcfirst($modelClassSingular) ?>Resource', function ($resource, $location, $state, $rootScope, $timeout, contentFilters, $q, DataEnvironmentService, suggestionsService) {

        // Silly stand-in for the default string object is necessary to work around the fact that
        // the url param in ngResource is only evaluated at $resource creation and can not be changed later
        var url = {};
        url.value = function() {
            //console.log('<?= lcfirst($modelClassSingular) ?> url.value()', this, angular.copy(env));
            return env.API_BASE_URL + '/' + env.API_VERSION + '/<?= lcfirst($modelClassSingular) ?>/:id'
        };
        url.split = function (separator,limit) { return url.value().split(separator,limit) };
        url.replace = function (match, other) { return url.value().replace(match, other) };
        url.toString = function() { return url.value(); }

        var resource = $resource(
            url,
            {id: '@id'},
            {
                query: {
                    method: 'GET',
                    isArray: true,
                    params: {
                        <?= $modelClassSingular ?>_page: 1,
                        <?= $modelClassSingular ?>_limit: 100
                    },

                    // The response is not a JSON array as expected by $resource but rather a
                    // JSON object that contains the actual result with additional metadata.
                    // It is therefore necessary to extract the payload before it can be
                    // processed by the resource.
                    transformResponse: function (data) {
                        if (!data) {
                            return data;
                        }
                        var wrappedResult = angular.fromJson(data);
                        wrappedResult.<?=$itemsResponseKey?>.$metadata = wrappedResult.<?=$metadataResponseKey?>;
                        return wrappedResult.<?=$itemsResponseKey?>;
                    },

                    // The response ist not a JSON array as expected by $resource but rather a
                    // JSON object that contains the actual result with additional metadata.
                    // It is therefore necessary to extract the payload before it can be
                    // processed by the resource.

                    // The array returned by transformResponse is not passed directly to the
                    // application logic, $resource only copies its contents. Due to this we
                    // cannot directly access the added metadata. But fortunately for us
                    // we can register a response interceptor in addition to transformResponse.
                    // Inside the interceptor we can access the array of instances that is
                    // returned by the query and the original data we parsed in
                    // transformResponse, so that we can add the metadata.
                    //
                    // CAVEAT: This depends on the fact that the actual result is exposed as
                    // response.resource which might change in future versions
                    interceptor: {
                        response: function (response) {
                            if (response.data && response.data.$metadata) {
                                response.resource.$metadata = response.data.$metadata;

                                // Inform angular that we have updated data by implicitly calling $apply via $timeout
                                $timeout(function () {
                                });

                            }
                            return response.resource;
                        }
                    }
                },
                update: {
                    method: 'PUT'
                }
            }
        );
        resource.dataSchema = function() {
            return {
                'id': null,
<?php if (in_array($modelClassSingular, array_keys(\ItemTypes::where('is_graph_relatable')))): ?>
                'node_id': null,
<?php endif; ?>
                'item_type': '<?= $itemTypeSingularRef ?>',
                'attributes': {
<?php
echo $this->render('../item-type-attributes-data-schema.inc.php', ["itemTypeAttributesWithAdditionalMetadata" => $itemTypeAttributesWithAdditionalMetadata, "level" => $level = 0, "modelClass" => $generator->modelClass]);
?>
                }
            };
        };
        resource.activeFilter = {};
        resource.getItemTypeFilter = function () {

            // Content filters
            return contentFilters.itemTypeSpecific("<?= $modelClassSingular ?>");

        };
        resource.getLocationBasedItemTypeFilter = function () {

            // Content filters
            return contentFilters.itemTypeSpecificLocationBasedContentFilters("<?= $modelClassSingular ?>");

        };
        resource.hasLocationBasedItemTypeFilter = function () {

            // Content filters
            return contentFilters.trueIfNonEmpty(contentFilters.itemTypeSpecificLocationBasedContentFilters("<?= $modelClassSingular ?>"));

        };
        // Collection is a ngResource facade that starts out empty and then gets populated during the refresh logic
        resource.collection = function (params) {

            var filter = resource.getItemTypeFilter();
            console.log('<?= lcfirst($modelClassSingular) ?> - filter', filter);

            var collection = [];
            collection.$metadata = {};
            collection.$resolved = null; // Neither true nor false, indicating that a request has not even begun

            // Collection-specific filter params
            collection.params = params || {};

            // Include a reference to the parent resource
            collection.$resource = resource;

            // A collection-specific scope for watches and event broadcasting
            collection.$scope = $rootScope.$new();

            // Returns a promise that waits for manual activation in ui code before the collection is populated from the server
            // (lazy load)
            collection.$activated = false;
            collection.deferredActivation = $q.defer();
            collection.$activate = function() {
                collection.deferredActivation.resolve();
                collection.$activated = true;
            };

            // Returns a promise which resolves the next time the collection is refreshed
            collection.newRefreshDeferredObject = function() {
                collection.refreshDeferredObject = $q.defer();
                return collection.refreshDeferredObject;
            };

            // Set initial refresh deferred object and promise
            collection.$promise = collection.newRefreshDeferredObject().promise;

            // State initial variable for "refreshing"-state
            collection.$refreshing = false;

            // State variable for current filter
            collection.filter = angular.copy(filter);

            // Method to check if current collection is filtered
            collection.filtered = function () {
                var filtersAreEmpty = angular.equals(collection.filter, {});
                return !filtersAreEmpty;
            };

            // Function to query/refresh the collection with items from server
            collection.refresh = function () {
                if (suggestionsService.status() === 'active') {
                    console.log('Warning: Can not refresh <?= lcfirst($modelClassPlural) ?> while operation previews are shown', collection);
                    // TODO: Notify UI that the operation previews must be refreshed in order for new data to show
                    collection.refreshDeferredObject.resolve(collection);
                    return collection.refreshDeferredObject.promise;
                }
                var filter = resource.getItemTypeFilter(); // necessary to not get outdated filter
                // Update state variable for current filter
                collection.filter = angular.copy(filter);
                var refreshedItems = resource.query(angular.merge(filter, collection.params));
                collection.$promise = refreshedItems.$promise;
                collection.$refreshing = true;
                refreshedItems.$promise.then(function () {
                    resource.activeFilter = angular.copy(filter);
                    collection.replace(refreshedItems);
                }).catch(function () {
                    collection.replace([]);
                }).finally(function () {
                    collection.$refreshing = false;
                    collection.$metadata = refreshedItems.$metadata;
                    collection.$resolved = refreshedItems.$resolved;
                    collection.refreshDeferredObject.resolve(collection);
                });
                return collection.refreshDeferredObject.promise;
            };

            collection.replace = function (items) {
                // empty original array then fill with the new items so that references to the collection (and thus view variables etc) are maintained
                collection.length = 0; // http://stackoverflow.com/questions/1232040/how-to-empty-an-array-in-javascript
                _.each(items, function (element, index, list) {
                    var resourceItem = new resource(element);
                    collection.push(resourceItem);
                });
                if (items.$metadata) {
                    collection.$metadata = items.$metadata;
                }
                collection.$scope.$broadcast('items.replaced', collection);
            };

            // Initial query when active data environment is available
            DataEnvironmentService.activeDataEnvironment.promise.then(function() {

                // automatic initial request inactivated for this item type - use collection.$activate() manually to populate collection from server
                collection.deferredActivation.promise.then(function() {
                    console.log('<?= lcfirst($modelClassPlural) ?> - initial query when collection has been activated');

                    collection.refresh();

                    // Activate refresh when filter has changed
                    collection.$scope.$watch(function ($scope) {
                            return resource.getItemTypeFilter();
                        },
                        function (newVal, oldVal) {
                            if (JSON.stringify(newVal) !== JSON.stringify(oldVal) && JSON.stringify(newVal) !== JSON.stringify(resource.activeFilter)) {
                                console.log('<?= lcfirst($modelClassPlural) ?>.refresh() due to getItemTypeFilter() change', newVal, oldVal);
                                collection.refresh();
                            }
                        },
                        true
                    );

                    // Activate refresh when active data environment has changed
                    $rootScope.$on('activeDataEnvironment.change', function (ev, chosenDataEnvironment) {
                        console.log('<?= lcfirst($modelClassPlural) ?>.refresh() due to "activeDataEnvironment.change" event', chosenDataEnvironment);
                        collection.refresh();
                    });

                });

            });

            // Function to add a new item (optionally with preset attributes) to the collection and server
            collection.add = function (itemAttributes, success, failure, atIndex) {
                var attributes = (itemAttributes ? angular.merge({}, resource.dataSchema(), itemAttributes) : resource.dataSchema());
                var newItem = new resource(attributes);
                // add item to collection
                if (typeof atIndex !== "undefined") {
                    collection.splice(atIndex, 0, newItem);
                } else {
                    collection.unshift(newItem);
                }
                // find index of item in collection
                var index = _.indexOf(collection, newItem);
                // add item on server
                newItem.$save(function (data) {
                    // success
                    console.log('<?= lcfirst($modelClassSingular) ?>.add(): data', data);
                    success && success(newItem);
                }, function (e) {
                    // on failure, remove item
                    collection.splice(index, 1);
                    failure && failure(e);
                });
            }

            // Function to add an existing item object to the collection (the item may or may not be available on the server)
            collection.addExisting = function (item) {
                // add item to collection
                collection.unshift(newItem);
            }

            // Function to remove an item from the collection and server
            collection.remove = function (id) {
                var item = _.find(collection, function (item) {
                    return item.attributes.id == id;
                });
                // find index of item in collection
                var index = _.indexOf(collection, item);
                // remove item from collection
                collection.splice(index, 1);
                // delete item on server
                item.$delete(function (data) {
                    // success
                    console.log('<?= lcfirst($modelClassSingular) ?>.remove(): data', data);
                }, function (e) {
                    // on failure, re-add item...
                    if (index > collection.length - 1) {
                        collection.push(item);
                    } else {
                        collection.splice(index, 0, item);
                    }
                });
            }

            // Function to remove an existing item object to the collection (the item may or may not be available on the server)
            collection.removeExisting = function (item) {
                // find index of item in collection
                var index = _.indexOf(collection, item);
                // remove item from collection
                collection.splice(index, 1);
            }

            /**
             * Tries to load from already fetched objects primarly, then from server. If no id is specified, load an empty new resource object
             * Use where it is assumed that the collection is already loaded, for quicker retrieval of single items
             * @param id
             * @returns {*}
             */
            collection.loadItem = function (id) {
                if (id) {

                    if (collection.currentItemInFocus && collection.currentItemInFocus.id == id) {
                        console.log('<?= lcfirst($modelClassSingular) ?> picked from collection.currentItemInFocus');
                        return collection.currentItemInFocus;
                    }

                    // Hot-load from singleton collection if already available
                    var item = _.find(collection, function (item) {
                        return item.attributes.id == id;
                    });
                    if (item) {
                        item.$promise = collection.$promise;
                        item.$resolved = collection.$resolved;
                        console.log('<?= lcfirst($modelClassSingular) ?> picked from collection');
                        return item;
                    }

                    console.log('<?= lcfirst($modelClassSingular) ?> fetched from server');
                    return resource.get({id: id});
                } else {
                    console.log('<?= lcfirst($modelClassSingular) ?> without id - assuming new <?= lcfirst($modelClassSingular) ?>');
                    return new resource(resource.dataSchema);
                }
            };

            collection.replaceItemInCollection = function (item, itemData) {
                _.each(Object.keys(resource.dataSchema()), function (key, index, list) {
                    delete item[key];
                });
                var resourceItem = new resource(itemData);
                _.each(itemData, function (value, key, list) {
                    item[key] = resourceItem[key];
                });
            };

            collection.shouldUseThisUpdatedItemIfExistsInCollection = function(updatedItem) {

                // Hot-load from singleton collection if already available
                var item = _.find(collection, function (item) {
                    return item.attributes.id == updatedItem.id;
                });
                if (item) {
                    collection.replaceItemInCollection(item, updatedItem);
                }

            };

            // Current-item-in-focus logic
            collection.currentIndex = function () {
                var item = _.find(collection, function (item) {
                    return item.attributes.id == $state.params.active<?= $modelClassSingular ?>Id;
                });
                return _.indexOf(collection, item);
            };

            collection.previousItem = function () {
                return collection[collection.currentIndex() - 1];
            };

            collection.nextItem = function () {
                return collection[collection.currentIndex() + 1];
            };

            collection.goToCurrentItemState = function(item) {
                var goToStateParams = angular.merge($state.params, {active<?= $modelClassSingular ?>Id: item.id});
                //$state.transitionTo($state.current.name, goToStateParams, { notify: false });
                if ($state.current.name.indexOf('.current-item') > -1) {
                    $state.go($state.current.name, goToStateParams);
                } else {
                    $state.go($state.current.name + '.current-item', goToStateParams);
                }
            };

            collection.setCurrentItemInFocus = function(item) {
                item.$promise = collection.$promise;
                item.$resolved = collection.$resolved;
                collection.currentItemInFocus = item;
                collection.goToCurrentItemState(item);
                // Inform angular that we have updated data by implicitly calling $apply via $timeout
                $timeout(function () {
                });
            };

            collection.clearCurrentItemInFocus = function() {
                collection.currentItemInFocus = null;
                // TODO:
                //collection.goToNoCurrentItemState(item);
            };

            return collection;

        };
        // Item is a ngResource facade that starts out empty and then gets populated during the refresh logic
        resource.item = function (params) {

            var item = {};
            item.$resolved = null; // Neither true nor false, indicating that a request has not even begun

            // Include the corresponding id
            item.$id = null;

            // Include a reference to the parent resource
            item.$resource = resource;

            // Returns a promise which resolves the first time the item is queried or next time the item is refreshed
            item.newRefreshDeferredObject = function() {
                item.refreshDeferredObject = $q.defer();
                return item.refreshDeferredObject;
            };

            // Set initial refresh deferred object and promise
            item.$promise = item.newRefreshDeferredObject().promise;

            // A promise that waits for manual activation in ui/route code before the item is populated from the server
            // (lazy load)
            item.$activated = false;
            item.deferredActivation = $q.defer();
            item.$activate = function() {
                item.deferredActivation.resolve();
                item.$activated = true;
                return item.refreshDeferredObject.promise;
            };

            // State initial variable for "refreshing"-state
            item.$refreshing = false;

            // Function to query/refresh the item from server
            item.refresh = function () {
                if (suggestionsService.status() === 'active') {
                    console.log('Warning: Can not refresh <?= lcfirst($modelClassSingular) ?> while operation previews are shown', item);
                    // TODO: Notify UI that the operation previews must be refreshed in order for new data to show
                    item.refreshDeferredObject.resolve(item);
                    return item.refreshDeferredObject.promise;
                }
                let refreshedItem;
                if (item.$id) {
                    refreshedItem = resource.get({id: item.$id});
                } else {
                    // This may be the case if we had a previous active item
                    // and now want to go back to an unloaded state
                    let emptyItem = new resource(resource.dataSchema());
                    let emptyItemDefer = $q.defer();
                    refreshedItem = emptyItem;
                    refreshedItem.$promise = emptyItemDefer.promise;
                    emptyItemDefer.resolve(emptyItem);
                }
                item.$promise = refreshedItem.$promise;
                item.$refreshing = true;
                refreshedItem.$promise.then(function () {
                    item.replace(refreshedItem);
                }).catch(function () {
                    item.replace({});
                }).finally(function () {
                    item.$refreshing = false;
                    item.$metadata = refreshedItem.$metadata;
                    item.$resolved = refreshedItem.$resolved;
                    item.$get = refreshedItem.$get;
                    item.$update = refreshedItem.$update;
                    item.refreshDeferredObject.resolve(item);
                });
                return item.refreshDeferredObject.promise;
            };

            item.replace = function (itemData) {
                _.each(Object.keys(resource.dataSchema()), function (key, index, list) {
                    delete item[key];
                });
                var resourceItem = new resource(itemData);
                _.each(itemData, function (value, key, list) {
                    item[key] = resourceItem[key];
                });
                //item.$scope.$broadcast('item.replaced', item);
            };

            item.load = function (id) {
                // TODO: Hot-load from singleton collection if already available
                item.$id = id;
                return item.refresh();
            };

            // Initial query when active data environment is available
            DataEnvironmentService.activeDataEnvironment.promise.then(function() {

                // automatic initial request inactivated for this item type - use item.$activate() manually to populate item from server
                item.deferredActivation.promise.then(function() {
                    console.log('<?= lcfirst($modelClassSingular) ?> - initial query when item has been activated');

                    item.refresh();

                    // Activate refresh when active data environment has changed
                    $rootScope.$on('activeDataEnvironment.change', function (ev, chosenDataEnvironment) {
                        console.log('<?= lcfirst($modelClassSingular) ?>.refresh() due to "activeDataEnvironment.change" event', chosenDataEnvironment);
                        item.refresh();
                    });

                });

            });

            return item;

        };
        return resource;
    })

    /**
     * Inject to get a singleton of populated modifiable array of items from database
     */
    .service('<?= lcfirst($modelClassPlural) ?>', function (<?= lcfirst($modelClassSingular) ?>Resource) {

        var collection = <?= lcfirst($modelClassSingular) ?>Resource.collection();
        return collection;

    })

    /**
     * Inject to get a singleton of populatable item object from database
     */
    .service('<?= lcfirst($modelClassSingular) ?>', function (<?= lcfirst($modelClassSingular) ?>Resource) {

        var item = <?= lcfirst($modelClassSingular) ?>Resource.item();
        return item;

    })

    /**
     * Service that contains the item's relations' metadata
     */
    .service('<?= lcfirst($modelClassSingular) ?>RelationsMetadata', function ($rootScope, $location, $timeout, $q, $ocLazyLoad, $injector) {

        // General relations logic
        var relations = {
<?php
foreach ($itemTypeAttributesWithAdditionalMetadata as $attribute => $attributeInfo):

    // Attribute referencing other item type's attribute
    $isDeepAttribute = strpos($attribute, '/') !== false;

    switch ($attributeInfo["type"]) {
        case "has-one-relation":
        case "has-many-relation":

            if ($isDeepAttribute) {
                $_ = explode("/", $attribute);
                $throughModelClassSingular = $_[0];
                $referencedAttribute = $_[1];
?>
            '<?=$attribute?>': $injector.get('<?= lcfirst($throughModelClassSingular) ?>RelationsMetadata').<?= $referencedAttribute ?>,
<?php
                continue;
            }

            if (!isset($attributeInfo['relatedModelClass'])) {
                throw new Exception(
                    "$modelClass.$attribute - No relation information available"
                );
            }
            $relatedModelClass = $attributeInfo['relatedModelClass'];

            $relatedModelClassSingular = $relatedModelClass;
            $relatedModelClassSingularWords = Inflector::camel2words($relatedModelClassSingular);
            $relatedModelClassSingularId = Inflector::camel2id($relatedModelClassSingular);
            $relatedItemTypeSingularRef = Inflector::camel2id($relatedModelClassSingular, '_');
            $relatedModelClassPluralWords = Inflector::pluralize($relatedModelClassSingularWords);
            $relatedModelClassPlural = Inflector::camelize($relatedModelClassPluralWords);

?>
            '<?=$attribute?>': {
                relatedCollection: function() {
                    // Inject the related collection
                    return $q((resolve) => {
                        $injector.invoke(['<?= lcfirst($relatedModelClassPlural) ?>', function (<?= lcfirst($relatedModelClassPlural) ?>) {
                            <?= lcfirst($relatedModelClassPlural) ?>.$activate();
                            <?= lcfirst($relatedModelClassPlural) ?>.$promise.then(function (collection) {
                                resolve(collection);
                            })
                        }]);
                    });
                },
<?php
if ($attributeInfo["type"] === "has-one-relation"):
?>
                select2Options: dnaProjectBaseHandsontableCrudHelper.defaultSelect2OptionsFactory('<?= lcfirst($relatedModelClassPlural) ?>', '<?= $relatedModelClassSingular ?>', $injector, $timeout, $location),
<?php
endif;
?>
            },
<?php
            break;
        default:
            // ignore
            break;
    }

endforeach;
?>
        };

        return relations;

    })

    /**
     * Service that contains the main objects for CRUD logic
     */
    .service('<?= lcfirst($modelClassSingular) ?>Crud', function ($rootScope, hotkeys, $location, $timeout, <?= lcfirst($modelClassPlural) ?>, <?= lcfirst($modelClassSingular) ?>RelationsMetadata ,$q, $ocLazyLoad, $injector) {

        // General relations logic
        var relations = <?= lcfirst($modelClassSingular) ?>RelationsMetadata;

        var handsontable = {

            afterChange: dnaProjectBaseHandsontableCrudHelper.defaultAfterChangeCallbackFactory(<?= lcfirst($modelClassPlural) ?>),
            afterSelectionEndByPropCallback: dnaProjectBaseHandsontableCrudHelper.defaultAfterSelectionEndByPropCallbackFactory(<?= lcfirst($modelClassPlural) ?>, $rootScope, relations, hotkeys),
            deleteButtonRenderer: dnaProjectBaseHandsontableCrudHelper.defaultDeleteButtonRendererFactory(<?= lcfirst($modelClassPlural) ?>),

            /**
             * Column-specific logic
             */
            columnLogic: {

<?php
foreach ($itemTypeAttributesWithAdditionalMetadata as $attribute => $attributeInfo):

    // Do not consider attributes referencing other item types
    if (strpos($attribute, '/') !== false) {
        continue;
    }

    switch ($attributeInfo["type"]) {
        case "has-one-relation":

            if (!isset($attributeInfo['relatedModelClass'])) {
                throw new Exception(
                    "$modelClass.$attribute - No relation information available"
                );
            }
            $relatedModelClass = $attributeInfo['relatedModelClass'];

            $relatedModelClassSingular = $relatedModelClass;
            $relatedModelClassSingularWords = Inflector::camel2words($relatedModelClassSingular);
            $relatedItemTypeSingularRef = Inflector::camel2id($relatedModelClassSingular, '_');
            $relatedModelClassPluralWords = Inflector::pluralize($relatedModelClassSingularWords);
            $relatedModelClassPlural = Inflector::camelize($relatedModelClassPluralWords);

?>
                '<?=$attribute?>': {
                    cellRenderer: dnaProjectBaseHandsontableCrudHelper.defaultCellrendererForHasOneRelatedItemFactory(<?= lcfirst($modelClassPlural) ?>, '<?=$attribute?>', hotkeys),
                    select2Options: _.extend({
                            onBeginEditing: dnaProjectBaseHandsontableCrudHelper.onBeginEditingCallbackThatRequiresManualEditorStart,
                            width: '400px'
                        },
                        relations.<?=$attribute?>.select2Options
                    )
                },
<?php
            break;
        default:
            // ignore
            break;
    }

endforeach;
?>
            }
        };

        /** Column definitions */
        handsontable.workflowColumns = {
<?php if ($workflowItem): ?>
<?php foreach ($model->flowSteps() as $step => $stepAttributes): ?>
            // step: <?= $step . "\n" ?>
            '<?= $step ?>': [
<?php foreach ($stepAttributes as $attribute): ?>
<?php
                $params = compact("step", "itemTypeAttributesWithAdditionalMetadata", "modelClass");
                echo $generator->prependActiveFieldForAttribute("handsontable-column-settings." . $attribute, $model, $params);
                echo $generator->activeFieldForAttribute("handsontable-column-settings." . $attribute, $model, $params);
                echo $generator->appendActiveFieldForAttribute("handsontable-column-settings." . $attribute, $model, $params);
                echo "\n";
                ?>
<?php endforeach;?>
            ],
<?php endforeach; ?>
<?php endif; ?>
        };
        handsontable.crudColumns = [
<?php foreach ($itemTypeAttributesWithAdditionalMetadata as $attribute => $attributeInfo): ?>
<?php
                $params = compact("step", "itemTypeAttributesWithAdditionalMetadata", "modelClass");
                echo $generator->prependActiveFieldForAttribute("handsontable-column-settings." . $attribute, $model, $params);
                echo $generator->activeFieldForAttribute("handsontable-column-settings." . $attribute, $model, $params);
                echo $generator->appendActiveFieldForAttribute("handsontable-column-settings." . $attribute, $model, $params);
                echo "\n";
                ?>
<?php endforeach ?>
        ];

        return {
            relations: relations,
            handsontable: handsontable
        };

    });

export default module;
