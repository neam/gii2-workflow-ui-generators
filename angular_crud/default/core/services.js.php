<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = get_class($model);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$itemTypeSingularRef = Inflector::camel2id($modelClassSingular, '_');
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);

?>
(function () {

    var module = angular.module('crud-<?= Inflector::camel2id($modelClassSingular) ?>-services', []);

    /**
     * Inject to get an object for querying, adding, removing items
     */
    module.service('<?= lcfirst($modelClassSingular) ?>Resource', function ($resource) {
        var resource = $resource(
            env.API_BASE_URL + '/' + env.API_VERSION + '/<?= lcfirst($modelClassSingular) ?>/:id',
            {id : '@id'},
            {
                query: {
                    method: 'GET',
                    isArray: true,
                    params: {
                        page: 1,
                        limit: 100
                    }
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
        return resource;
    });

    /**
     * Inject to get an actual populated modifiable array of items from database
     */
    module.service('<?= lcfirst($modelClassPlural) ?>', function (<?= lcfirst($modelClassSingular) ?>Resource) {

        // Collection
        var collection = <?= lcfirst($modelClassSingular) ?>Resource.query();

        // Function to add a new item to the collection
        collection.add = function () {
            var newItem = new <?= lcfirst($modelClassSingular) ?>Resource(<?= lcfirst($modelClassSingular) ?>Resource.dataSchema);
            // add item to collection
            collection.unshift(newItem);
            // find index of item in collection
            var index = _.indexOf(collection, item);
            // add item on server
            newItem.$save(function(data) {
                // success
                console.log('<?= lcfirst($modelClassSingular) ?>.add(): data', data);
            }, function(e) {
                // on failure, remove item
                collection.splice(index, 1);
            });
        }

        // Function to remove an item from the collection
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

        return collection;
    });

    /**
     * Service that contains the main objects for CRUD logic
     */
    module.service('<?= lcfirst($modelClassSingular) ?>Crud', function (<?= lcfirst($modelClassPlural) ?><?php
        $hasOneRelatedModelClasses = $generator->hasOneRelatedModelClasses();
        foreach ($hasOneRelatedModelClasses as $hasOneRelatedModelClass):
        $hasOneRelatedModelClassSingularWords = Inflector::camel2words($hasOneRelatedModelClass);
        $hasOneRelatedModelClassPluralWords = Inflector::pluralize($hasOneRelatedModelClassSingularWords);
        $hasOneRelatedModelClassPlural = Inflector::camelize($hasOneRelatedModelClassPluralWords);
            ?>, <?= lcfirst($hasOneRelatedModelClassPlural) ?><?php endforeach; ?>) {

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

                    if (source === 'edit') {
                        changeObject.id = self.getDataAtRowProp(changeObject.row, 'attributes.id');
                        editObjects.push(changeObject);
                    }

                    if (source === 'empty') {
                        newObjects.push(changeObject);
                    }

                });

                console.log('<?= lcfirst($modelClassSingular) ?>Crud: <?= lcfirst($modelClassPlural) ?>, editObjects, newObjects', <?= lcfirst($modelClassPlural) ?>, editObjects, newObjects);

                function setDepth(obj, path, value) {
                    var tags = path.split("."), len = tags.length - 1;
                    for (var i = 0; i < len; i++) {
                        obj = obj[tags[i]];
                    }
                    obj[tags[len]] = value;
                }

                _.each(editObjects, function (changeObject, index, list) {

                    var item = _.find(<?= lcfirst($modelClassPlural) ?>, function (item) {
                        return item.attributes.id == changeObject.id;
                    });

                    console.log('<?= lcfirst($modelClassSingular) ?>Crud: changeObject, item', changeObject, item);

                    setDepth(item, changeObject.prop, changeObject.newVal);
                    item.$save(function (savedObject, putResponseHeaders) {
                        console.log('<?= lcfirst($modelClassSingular) ?>Crud: savedObject', savedObject);
                        //putResponseHeaders => $http header getter
                    });

                });

            },

            deleteButtonRenderer: function (instance, td, row, col, prop, value, cellProperties) {
                var $button = $('<button>Delete</button>');

                var id = instance.getDataAtRowProp(row, 'attributes.id');

                //$button.html(value);
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
                    cellRenderer: function (instance, td, row, col, prop, value, cellProperties) {

                        var rowItemId = instance.getDataAtRowProp(row, 'attributes.id');
                        var item = _.find(<?= lcfirst($modelClassPlural) ?>, function (item) {
                            return item.attributes.id == rowItemId;
                        });

                        Handsontable.TextCell.renderer.apply(this, arguments);

                        value = item.attributes.<?=$attribute?>.item_label;
                        var markup = value;
                        td.innerHTML = markup;
                        return td;

                    },
                    select2Options: {
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