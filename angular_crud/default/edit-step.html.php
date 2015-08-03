<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

?>
<!--
Template for <?= ltrim($generator->modelClass, '\\') ?> model, step "<?=$step?>"
-->
?>
<?php foreach ($attributes as $attribute): ?>

<!-- <?= $attribute ?> --><?php

    $prepend = $generator->prependActiveFieldForAttribute($attribute, $model);
    $field = $generator->activeFieldForAttribute($attribute, $model);
    $append = $generator->appendActiveFieldForAttribute($attribute, $model);

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

