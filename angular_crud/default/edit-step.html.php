<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

?>
<!--
Template for <?= ltrim($generator->modelClass, '\\') ?> model, step "<?=$step?>"
-->

<div class="ibox float-e-margins ibox-top-margin">
    <div class="ibox-content">
        <fieldset>
            <div class="form-group cfg">

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

            </div>
        </fieldset>
    </div>
</div>
