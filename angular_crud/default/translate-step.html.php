<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

?>
<!--
Template for <?= ltrim($generator->modelClass, '\\') ?> model, translate step "<?=$step?>"
-->
?>
<?php foreach ($translatableAttributes as $attribute => $sourceLanguageContentAttribute): ?>

<!-- <?= $attribute ?> --><?php

    $prepend = $generator->prependActiveFieldForAttribute($sourceLanguageContentAttribute, $model);
    $field = $generator->activeFieldForAttribute($sourceLanguageContentAttribute, $model);
    $append = $generator->appendActiveFieldForAttribute($sourceLanguageContentAttribute, $model);

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

