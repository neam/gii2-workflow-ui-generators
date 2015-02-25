<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

echo "<?php\n";
?>

class <?=$generator->controllerClass?> extends <?=$generator->baseControllerClass."\n"?>
{

    use GridviewControllerActionsTrait;
    use WorkflowUiControllerTrait;

    public $modelClass = "<?=$generator->modelClass?>";
    public $defaultAction = "browse";

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array_merge($this->itemAccessRules(), array(
            array(
                'allow',
                'actions' => array(
                    'customAction', // placeholder - rename when/if you add the first custom action
                ),
                'users' => array('@'),
            ),
            array(
                'deny',
                'users' => array('*'),
            ),
        ));
    }

    /**
     * @param int $id the model id.
     * @return <?=$generator->modelClass."\n"?>
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = <?=$generator->modelClass?>::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, Yii::t('model', 'The requested page does not exist.'));
        }
        return $model;
    }

    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && ($_POST['ajax'] === '<?=Inflector::camel2id($generator->modelClass)?>-form' || $_POST['ajax'] === 'item-form')) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
