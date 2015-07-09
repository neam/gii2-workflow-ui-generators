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
            {},
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
            $relatedModelClass = "RestApi".$relationInfo[1];

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
            $relatedModelClass = "RestApi".$relationInfo[1];

?>
                '<?=$attribute?>': {},
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

        // Function to add a placeholder for new item in collection
        collection.addPlaceholder = function () {
            var newItem = new <?= lcfirst($modelClassSingular) ?>Resource(<?= lcfirst($modelClassSingular) ?>Resource.dataSchema);
            newItem.$save();
            collection.push(newItem);
        }

        // Uncomment to automatically add a placeholder for new item in collection
        /*
        collection.$promise.then(function () {
            collection.addPlaceholder();
        });
        */

        return collection;
    });

    /**
     * Service that contains the main objects for CRUD logic
     */
    module.service('<?= lcfirst($modelClassSingular) ?>Crud', function (<?= lcfirst($modelClassPlural) ?>) {

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

            }

        }

        return {
            handsontable: handsontable
        };

    });


})();