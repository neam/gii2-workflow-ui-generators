<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = get_class($model);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$itemTypeSingularRef = Inflector::camel2id($modelClassSingular, '_');
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);

// http://www.yiiframework.com/doc-2.0/guide-rest-response-formatting.html
$itemsResponseKey = 'items';
$metadataResponseKey = '_meta';

?>
(function () {

    var module = angular.module('crud-<?= Inflector::camel2id($modelClassSingular) ?>-services', []);

    /**
     * Inject to get an object for querying, adding, removing items
     */
    module.service('<?= lcfirst($modelClassSingular) ?>Resource', function ($resource, $location, $rootScope) {
        var resource = $resource(
            env.API_BASE_URL + '/' + env.API_VERSION + '/<?= lcfirst($modelClassSingular) ?>/:id',
            {id : '@id'},
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
                            response.resource.$metadata = response.data.$metadata;

                            // Tmp workaround for the fact that <?= lcfirst($modelClassPlural) ?>.$metadata is not watchable (no change is detected, even on equality watch) from the controller scope for whatever reason
                            $rootScope.$broadcast('<?= $modelClassSingular ?>_metadataUpdated', response.resource.$metadata);

                            return response.resource;
                        }
                    }
                },
                update: {
                    method: 'PUT'
                }
            }
        );
        resource.dataSchema = {
            'id': null,
<?php if (in_array($modelClassSingular, array_keys(\ItemTypes::where('is_graph_relatable')))): ?>
            'node_id': null,
<?php endif; ?>
            'item_type': '<?= $itemTypeSingularRef ?>',
            'attributes': {
<?php
if (!method_exists($model, 'itemTypeAttributes')) {
    throw new Exception("Model ".get_class($model)." does not have method itemTypeAttributes()");
}
$relations = $model->relations();
foreach ($model->itemTypeAttributes() as $attribute => $attributeInfo):

    switch ($attributeInfo["type"]) {
        case "has-many-relation":
        case "many-many-relation":

            if (!isset($relations[$attribute])) {
                throw new Exception("Model ".get_class($model)." does not have a relation '$attribute'");
            }
            $relationInfo = $relations[$attribute];
            $relatedModelClass = $relationInfo[1];

            // tmp until memory allocation has been resolved
            break;

?>
                '<?=$attribute?>': [],
<?php
            break;
        case "has-one-relation":
        case "belongs-to-relation":

            if (!isset($relations[$attribute])) {
                throw new Exception("Model ".get_class($model)." does not have a relation '$attribute'");
            }
            $relationInfo = $relations[$attribute];
            $relatedModelClass = $relationInfo[1];

?>
                '<?=$attribute?>': {id: null, item_label: null, item_type: 'todo'},
<?php
            break;
        case "ordinary":
        case "primary-key":
?>
                '<?=$attribute?>': null,
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
        resource.collection = function() {

            // Use $location.search as params
            var filter = $location.search();

            // Collection
            var collection = resource.query(filter);

            // State variable for "refreshing"-state
            collection.$refreshing = false;

            // State variable for current filter
            collection.filter = angular.copy(filter);

            // Method to check if current collection is filtered
            collection.filtered = function() {
                return angular.equals(collection.filter, {});
            };

            // Function to refresh the collection with items from server
            collection.refresh = function () {
                var filter = $location.search(); // necessary to not get outdated filter
                // Update state variable for current filter
                collection.filter = angular.copy(filter);
                var refreshedItems = resource.query(filter);
                collection.$refreshing = true;
                refreshedItems.$promise.then(function () {
                    collection.$refreshing = false;
                    collection.replace(refreshedItems);
                });
            };

            collection.replace = function (items) {
                // empty original array then fill with the new items so that references to the collection (and thus view variables etc) are maintained
                collection.length = 0; // http://stackoverflow.com/questions/1232040/how-to-empty-an-array-in-javascript
                _.each(items, function (element, index, list) {
                    var resourceItem = new resource(element);
                    collection.push(resourceItem);
                });
            };

            // Function to add a new item (optionally with preset attributes) to the collection and server
            collection.add = function (itemAttributes, success, failure) {
                var attributes = (itemAttributes ? angular.extend({}, resource.dataSchema, itemAttributes) : resource.dataSchema);
                var newItem = new resource(attributes);
                // add item to collection
                collection.unshift(newItem);
                // find index of item in collection
                var index = _.indexOf(collection, newItem);
                // add item on server
                newItem.$save(function(data) {
                    // success
                    console.log('<?= lcfirst($modelClassSingular) ?>.add(): data', data);
                    success && success(newItem);
                }, function(e) {
                    // on failure, remove item
                    collection.splice(index, 1);
                    failure && failure();
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
                // remote item from collection
                collection.splice(index, 1);
                // delete item on server
                item.$delete(function(data) {
                    // success
                    console.log('<?= lcfirst($modelClassSingular) ?>.remove(): data', data);
                }, function(e) {
                    // on failure, re-add item...
                    if (index > collection.length-1) {
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
                // remote item from collection
                collection.splice(index, 1);
            }

            return collection;

        };
        return resource;
    });

    /**
     * Inject to get a singleton of populated modifiable array of items from database
     */
    module.service('<?= lcfirst($modelClassPlural) ?>', function (<?= lcfirst($modelClassSingular) ?>Resource) {

        var collection = <?= lcfirst($modelClassSingular) ?>Resource.collection();
        return collection;

    });

    /**
     * Service that contains the main objects for CRUD logic
     */
    module.service('<?= lcfirst($modelClassSingular) ?>Crud', function ($rootScope, hotkeys, <?= lcfirst($modelClassPlural) ?><?php
        $hasOneRelatedModelClasses = $generator->hasOneRelatedModelClasses();
        foreach ($hasOneRelatedModelClasses as $hasOneRelatedModelClass):
        $hasOneRelatedModelClassSingularWords = Inflector::camel2words($hasOneRelatedModelClass);
        $hasOneRelatedModelClassPluralWords = Inflector::pluralize($hasOneRelatedModelClassSingularWords);
        $hasOneRelatedModelClassPlural = Inflector::camelize($hasOneRelatedModelClassPluralWords);
            ?>, <?= lcfirst($hasOneRelatedModelClassPlural) ?><?php endforeach; ?>) {

        // A singleton service-specific scope that we use to make that there is always only a single set of column-specific keycombos active at a time
        if (!$rootScope.$columnSpecificKeyComboScope) {
            $rootScope.$columnSpecificKeyComboScope = $rootScope.$new();
        }

        var reset$columnSpecificKeyComboScope = function() {
            $rootScope.$columnSpecificKeyComboScope.$destroy();
            $rootScope.$columnSpecificKeyComboScope = $rootScope.$new();
        };

        /**
         * Used to prevent select2 editor opening when not desired by hooking into select2-handsontable's onBeginEditing callback
         * for which a false return value results in that the editor does not open.
         *
         * @param initialValue
         * @param event
         * @returns {boolean}
         */
        var onBeginEditingCallbackThatRequiresManualEditorStart = function (initialValue, event) {
            console.log('onBeginEditingCallbackThatRequiresManualEditorStart', initialValue, event);
            // Assume undefined is from double-click since onDblClick() runs openEditor() without arguments
            if (!initialValue && !event) {
                return true;
            }
            // Allow only opening via ENTER and F2
            var keyCodes = Handsontable.helper.keyCode;
            var ctrlDown = (event.ctrlKey || event.metaKey) && !event.altKey;
            if (ctrlDown) {
                return false;
            }
            switch (event.keyCode) {
                case keyCodes.F2:
                case keyCodes.ENTER:
                    return true;
            }
            return false;
        };

        var handsontable = {

            /**
             * Handsontable afterChange method that uses $resource to save new records and update existing
             *
             * @param change 2D array containing information about each of the edited cells [[row, prop, oldVal, newVal], ...]
             * @param source one of the strings: "alter", "empty", "edit", "populateFromArray", "loadData", "autofill", "paste".
             */
            afterChange: function(changes, source) {

                if (source === 'loadData') {
                    return; // for performance reasons, the changes array is null for "loadData" source.
                }

                console.log('<?= lcfirst($modelClassSingular) ?>Crud: changes, source, this, $(this)', changes, source, this, $(this));

                var editObjects = [];
                var newObjects = [];
                var self = this;

                _.each(changes, function (change, index, list) {

                    var changeObject = {
                        "row": change[0],
                        "prop": change[1],
                        "oldVal": change[2],
                        "newVal": change[3]
                    };

                    // Ignore non-changes
                    if (changeObject.oldVal === changeObject.newVal) {
                        return;
                    }

                    if (source === 'edit' || source === 'paste') {
                        changeObject.id = self.getDataAtRowProp(changeObject.row, 'attributes.id');
                        editObjects.push(changeObject);
                    }

                    if (source === 'empty') {
                        newObjects.push(changeObject);
                    }

                });

                //console.log('<?= lcfirst($modelClassSingular) ?>Crud: <?= lcfirst($modelClassPlural) ?>, editObjects, newObjects', <?= lcfirst($modelClassPlural) ?>, editObjects, newObjects);
                console.log('<?= lcfirst($modelClassSingular) ?>Crud: editObjects, newObjects', editObjects, newObjects);

                function setDepth(obj, path, value) {
                    var tags = path.split("."), len = tags.length - 1;
                    for (var i = 0; i < len; i++) {
                        obj = obj[tags[i]];
                    }
                    obj[tags[len]] = value;
                }

                _.each(editObjects, function (editObjects, index, list) {

                    var item = _.find(<?= lcfirst($modelClassPlural) ?>, function (item) {
                        return item.attributes.id == editObjects.id;
                    });

                    console.log('<?= lcfirst($modelClassSingular) ?>Crud: editObjects, item', editObjects, item);

                    setDepth(item, editObjects.prop, editObjects.newVal);
                    item.$update(function (savedObject, putResponseHeaders) {
                        console.log('<?= lcfirst($modelClassSingular) ?>Crud: savedObject', savedObject);
                        //putResponseHeaders => $http header getter
                    });

                });

                _.each(newObjects, function (newObjects, index, list) {

                    console.log('<?= lcfirst($modelClassSingular) ?>Crud: newObjects', newObjects);

                    // create new item
                    <?= lcfirst($modelClassPlural) ?>.add(); // TODO: add at the correct index where the new row was inserted

                });

            },

            /**
             * After selection end callback (with arguments to callback with property instead of column numbers)
             * responsible for registering and de-registering key combos for updating the currently selected cell
             *
             * @param row
             * @param property
             * @param row2
             * @param property2
             */
            afterSelectionEndByPropCallback: function (row, property, row2, property2) {
                console.log('afterSelectionEndByPropCallback', row, property, row2, property2, this);

                // Skip if more than one column is selected
                if (property !== property2) {
                    console.log('reset due to multiple columns selected');
                    reset$columnSpecificKeyComboScope();
                    return;
                }

                // Skip if editor is open
                var instance = this;
                var activeEditor = instance.getActiveEditor();
                if (activeEditor.isOpened()) {
                    console.log('reset due to open editor');
                    reset$columnSpecificKeyComboScope();
                    return;
                }

                // Get column name
                var selectedColumn = property.replace("attributes.", "").replace(".id", "");

                // Skip if not a relevant column
                if (!handsontable.columnLogic[selectedColumn] || !handsontable.columnLogic[selectedColumn].relatedCollection) {
                    console.log('reset due to no existing key combo configuration');
                    reset$columnSpecificKeyComboScope();
                    return;
                }

                // The cell-specific callback for key combos
                var onKeyCombo = function (item) {
                    // Set the cell values to the item id
                    var firstSelectedRow = Math.min(row, row2);
                    var lastSelectedRow = Math.max(row, row2);
                    for (i = firstSelectedRow; i < lastSelectedRow+1; i++) {
                        instance.setDataAtRowProp(i, property, item.id);
                    }
                    // Select the cell directly beneath the previous selection, if not already on last row
                    var lastRow = instance.countRows();
                    if (lastSelectedRow < lastRow) {
                        instance.selectCellByProp(lastSelectedRow+1, property);
                    }
                };

                // Manage keyboard shortcuts related to collection
                var keyComboManager = {
                    activateKeyCombos: function (collection) {

                        // Find key combos
                        var itemsWithShortcuts = _.filter(collection, function (item) {
                            return item.attributes.key_combo;
                        });

                        _.each(itemsWithShortcuts, function (item, index, list) {

                            // Delete existing if exists
                            hotkeys.del(item.attributes.key_combo);

                            // Add key combo
                            // when you bind it to the controller's scope, it will automatically unbind
                            // the hotkey when the scope is destroyed (due to ng-if or something that changes the DOM)
                            hotkeys.bindTo($rootScope.$columnSpecificKeyComboScope)
                                .add({
                                    combo: item.attributes.key_combo,
                                    description: 'Set value of current cell to "' + item.item_label + '"',
                                    callback: function (e) {
                                        onKeyCombo(item);
                                    }
                                });
                        });

                        console.log('activateKeyCombos - itemsWithShortcuts', itemsWithShortcuts);

                    },
                    deactivateKeyCombos: function (collection) {

                        // Find key combos
                        var itemsWithShortcuts = _.filter(collection, function (item) {
                            return item.attributes.key_combo;
                        });

                        // Delete key combos
                        _.each(itemsWithShortcuts, function (item, index, list) {
                            hotkeys.del(item.attributes.key_combo);
                        });

                    }
                }

                var collection = handsontable.columnLogic[selectedColumn].relatedCollection;

                // Remove cell-specific key combos not related to collection by resetting keycombo binding object
                reset$columnSpecificKeyComboScope();

                // Add key combos for collection
                keyComboManager.activateKeyCombos(collection);

                // Update key combos when underlying data changes
                $rootScope.$columnSpecificKeyComboScope.collection = collection;
                $rootScope.$columnSpecificKeyComboScope.$watch('collection', function (newCollection, oldCollection) {

                    console.log('$rootScope.$columnSpecificKeyComboScope.$watch newCollection - length', newCollection.length);

                    // Delete keyboard shortcuts found in old collection
                    keyComboManager.deactivateKeyCombos(oldCollection);

                    // Add key combos for new collection
                    keyComboManager.activateKeyCombos(newCollection);

                }, true);

            },

            deleteButtonRenderer: function (instance, td, row, col, prop, value, cellProperties) {
                var $button = $('<a href="javascript:void(0);"><i class="fa fa-icon-large fa-trash-o" style="color: red;"></i></a>');

                var id = instance.getDataAtRowProp(row, 'attributes.id');

                $button.click(function() {

                    console.log('delclick');

                    <?= lcfirst($modelClassPlural) ?>.remove(id);
                });
                $(td).empty().append($button); //empty is needed because you are rendering to an existing cell
            },

            /**
             * Column-specific logic
             */
            columnLogic: {

<?php
foreach ($model->itemTypeAttributes() as $attribute => $attributeInfo):

    switch ($attributeInfo["type"]) {
        case "has-one-relation":

            if (!isset($relations[$attribute])) {
                throw new Exception("Model ".get_class($model)." does not have a relation '$attribute'");
            }
            $relationInfo = $relations[$attribute];
            $relatedModelClass = $relationInfo[1];

            $relatedModelClassSingular = $relatedModelClass;
            $relatedModelClassSingularWords = Inflector::camel2words($relatedModelClassSingular);
            $relatedItemTypeSingularRef = Inflector::camel2id($relatedModelClassSingular, '_');
            $relatedModelClassPluralWords = Inflector::pluralize($relatedModelClassSingularWords);
            $relatedModelClassPlural = Inflector::camelize($relatedModelClassPluralWords);

?>
                '<?=$attribute?>': {
                    relatedCollection: <?= lcfirst($relatedModelClassPlural) ?>,
                    cellRenderer: function (instance, td, row, col, prop, value, cellProperties) {

                        var rowItemId = instance.getDataAtRowProp(row, 'attributes.id');
                        var item = _.find(<?= lcfirst($modelClassPlural) ?>, function (item) {
                            return item.attributes.id == rowItemId;
                        });

                        Handsontable.TextCell.renderer.apply(this, arguments);

                        value = item.attributes.<?=$attribute?>.item_label;

                        if (value === '[[none]]' /* && item.attributes.<?=$attribute?>.id === null Not using requirement since id sets first on updates before label is updated. If anything, shown loader or similar */) {

                            // Empty value = Suggest selection or add new

                            // Select button
                            var $selectButton = $('<a href="javascript:void(0);"><i class="fa fa-icon-large fa-caret-down" style="color: blue;"></i></a>');
                            $selectButton.click(function (event) {

                                console.log('selclick');

                                Handsontable.Dom.enableImmediatePropagation(event);
                                event.stopImmediatePropagation();
                                event.preventDefault();

                                // Open editor
                                var activeEditor = instance.getActiveEditor();
                                var keyCodes = Handsontable.helper.keyCode;
                                var keyboardEvent = jQuery.Event('keydown');
                                keyboardEvent.keyCode = keyboardEvent.which = keyCodes.F2;
                                var initialValue = value;
                                activeEditor.beginEditing(initialValue, keyboardEvent);
                                keyboardEvent.preventDefault();

                            });
                            var $addButton = $('<a href="javascript:void(0);"><i class="fa fa-icon-large fa-plus-circle" style="color: green;"></i></a>');
                            $addButton.click(function (event) {

                                console.log('addclick');

                                Handsontable.Dom.enableImmediatePropagation(event);
                                event.stopImmediatePropagation();
                                event.preventDefault();

                                // add an item to the collection
                                <?= lcfirst($relatedModelClassPlural) ?>.add({}, function(newItem) {

                                    // success callback sets this cell value to the new id
                                    instance.setDataAtRowProp(row, 'attributes.<?=$attribute?>.id', newItem.id);

                                });

                            });
                            var $keyComboButton = $('<a href="javascript:void(0);"><i class="fa fa-icon-large fa-keyboard-o" style="color: black;"></i></a>');
                            $keyComboButton.click(function (event) {

                                console.log('keycomboclick start');

                                Handsontable.Dom.enableImmediatePropagation(event);
                                event.stopImmediatePropagation();
                                event.preventDefault();

                                hotkeys.toggleCheatSheet();

                                console.log('keycomboclick end');

                            });
                            $(td).empty() //empty is needed because we are rendering to an existing cell
                                .append($selectButton)
                                .append($('<span>&nbsp;</span>'))
                                .append($addButton)
                                .append($('<span>&nbsp;</span>'))
                                .append($keyComboButton);

                        } else {

                            // Existing value, render value
                            var markup = value;
                            td.innerHTML = markup;

                        }

                        return td;

                    },
                    select2Options: {
                        onBeginEditing: onBeginEditingCallbackThatRequiresManualEditorStart,
                        /**
                         * Ajax mode in select2 is used for two reasons:
                         * 1. It allows the input to be initialized before the selections/options are available
                         * 2. It allows for selection amongst an unlimited amount of items
                         *
                         * However, the transport function is overridden as to not actually use the built in jquery ajax methods,
                         * but instead use the ngResource representation of the alternatives.
                         */
                        ajax: {
                            dataType: 'json',
                            delay: 0,
                            data: function (params) {
                                return {
                                    q: params.term, // search term
                                    page: params.page
                                };
                            },
                            processResults: function (data, page) {
                                // parse the results into the format expected by Select2.
                                // since we are using custom formatting functions we do not need to
                                // alter the remote JSON data
                                return {
                                    results: data
                                };
                            },
                            // @param params The object containing the parameters used to generate the
                            //   request.
                            // @param success A callback function that takes `data`, the results from the
                            //   request.
                            // @param failure A callback function that indicates that the request could
                            //   not be completed.
                            // @returns An object that has an `abort` function that can be called to abort
                            //   the request if needed.
                            transport: function (params, success, failure) {

                                <?= lcfirst($relatedModelClassPlural) ?>.$promise.then(function () {

                                    // Filter available results to match the entered text
                                    var filtered = _.filter(<?= lcfirst($relatedModelClassPlural) ?>, function (data, iterator, context) {

                                        // If there are no search terms, return all of the data
                                        if ($.trim(params.data.q) === '') {
                                            return true;
                                        }

                                        if (data.item_label.toLowerCase().indexOf(params.data.q.toLowerCase()) > -1) {
                                            return true
                                        }

                                        return false;
                                    });

                                    // Add a choice for not selecting anything
                                    var choices = _.union([
                                        {
                                            id: "",
                                            item_label: '&lt;none&gt;'
                                        }
                                    ], filtered);

                                    success(choices);

                                }).catch(failure);

                                var returnObject = {
                                    abort: function () {
                                        console.log('abort TODO');
                                    }
                                };
                                return returnObject;

                            },
                            cache: true
                        },
                        // Perform no escape of markup = allow html
                        escapeMarkup: function (markup) {
                            return markup;
                        },
                        minimumInputLength: 0,
                        templateResult: function (item) {
                            return item.item_label;
                        },
                        templateSelection: function (item) {
                            return item.item_label;
                        },
                        dropdownAutoWidth: true,
                        width: '400px'
                    }
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
        }

        return {
            handsontable: handsontable
        };

    });


})();