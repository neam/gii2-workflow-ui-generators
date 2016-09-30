<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();
$modelClass = $generator->modelClass;

?>
<!--
Template for <?= ltrim($modelClass, '\\') ?> model, step "<?=$step?>"
-->

<?php foreach ($attributes as $attribute): ?>

<!-- <?= $attribute ?> --><?php

    $prepend = $generator->prependActiveFieldForAttribute($attribute, $model, compact("itemTypeAttributesWithAdditionalMetadata", "modelClass"));
    $field = $generator->activeFieldForAttribute($attribute, $model, compact("itemTypeAttributesWithAdditionalMetadata", "modelClass"));
    $append = $generator->appendActiveFieldForAttribute($attribute, $model, compact("itemTypeAttributesWithAdditionalMetadata", "modelClass"));

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
