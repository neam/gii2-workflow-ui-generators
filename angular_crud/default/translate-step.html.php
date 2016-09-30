<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();
$modelClass = $generator->modelClass;

?>
<!--
Template for <?= ltrim($modelClass, '\\') ?> model, translate step "<?=$step?>"
-->
?>
<?php foreach ($translatableAttributes as $attribute => $sourceLanguageContentAttribute): ?>

<!-- <?= $attribute ?> --><?php

    $prepend = $generator->prependActiveFieldForAttribute($sourceLanguageContentAttribute, $model, compact("itemTypeAttributesWithAdditionalMetadata", "modelClass"));
    $field = $generator->activeFieldForAttribute($sourceLanguageContentAttribute, $model, compact("itemTypeAttributesWithAdditionalMetadata", "modelClass"));
    $append = $generator->appendActiveFieldForAttribute($sourceLanguageContentAttribute, $model, compact("itemTypeAttributesWithAdditionalMetadata", "modelClass"));

    if ($prepend) {
        echo "\n" . $prepend . "";
    }
    if ($field) {
        echo "\n" . $field . "";
    }
    if ($append) {
        echo "\n" . $append . "";
    }

endforeach; ?>

