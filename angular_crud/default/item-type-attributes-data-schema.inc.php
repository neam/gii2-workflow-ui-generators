<?php
if ($level > 1) {
    echo "// Recursion limit reached\n";
    return;
}
$level++;

foreach ($itemTypeAttributesWithAdditionalMetadata as $attribute => $attributeInfo):

    // Deep attributes are handled indirectly via their parent attributes
    if (strpos($attribute, '/') !== false) {
        continue;
    }

    switch ($attributeInfo["type"]) {
        case "has-many-relation":
        case "many-many-relation":

            if (!isset($attributeInfo['relatedModelClass'])) {
?>
            // "<?=$modelClass?>.<?=$attribute?>" - No relation information available
<?php
                break;
            }

            // hint that an array is expected
?>
            '<?=$attribute?>': [],
<?php
            break;
        case "has-one-relation":
        case "belongs-to-relation":

            if (!isset($attributeInfo['relatedModelClass'])) {
                throw new Exception(
                    "$modelClass.$attribute - No relation information available"
                );
            }
?>
            '<?=$attribute?>': {
                id: $state.params.<?=$attribute."Id"?> || null,
                item_label: null,
                item_type: 'todo',
<?php if (array_key_exists('deepAttributes', $attributeInfo)): ?>
                // Note: Supplying attributes info in the data schema implies that the relation should always be available and will thus be created automatically
                attributes: {
<?php
echo $this->render('item-type-attributes-data-schema.inc.php', ["itemTypeAttributesWithAdditionalMetadata" => $attributeInfo['deepAttributes'], "level" => $level, "modelClass" => $attributeInfo["relatedModelClass"]]);
?>
                },
<?php endif; ?>
            },
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
