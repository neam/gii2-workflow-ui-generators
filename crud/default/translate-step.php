<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

echo "<?php\n";
?>
/**
* @var <?= ltrim($generator->controllerClass, '\\') ?>|WorkflowUiControllerTrait $this
* @var <?= ltrim($generator->modelClass, '\\') ?>|ItemTrait $model
* @var TbActiveForm $form
*/
?>

<?php
//var_dump($translatableAttributes);
?>

<?php foreach ($translatableAttributes as $attribute => $sourceLanguageContentAttribute): ?>

    <?php

    $prepend = $generator->prependActiveFieldForAttribute($attribute, $model);
    $field = $generator->activeFieldForAttribute($attribute, $model);
    $append = $generator->appendActiveFieldForAttribute($attribute, $model);

    if ($prepend) {
        echo "\n\t\t\t<?= " . $prepend . " ?>";
    }
    if ($field) {
        echo "\n\t\t\t<?= " . $field . " ?>";
    }
    if ($append) {
        echo "\n\t\t\t<?= " . $append . " ?>";
    }

    /*
    <?="<?php";?> echo $form->translateTextFieldControlGroup(
        $model,
        '<?=$attribute?>',
        $this->getTranslationLanguage(),
        $this->action->id,
        array('hint' => true)
    ); ?>

        */
    ?>

<?php endforeach; ?>
