<?php

namespace neam\gii2_workflow_ui_generators\yii1_crud\providers;

class CallbackProvider extends \schmunk42\giiant\base\Provider
{
    public $activeFields = [];
    public $prependActiveFields = [];
    public $appendActiveFields = [];
    public $attributeFormats = [];
    public $columnFormats = [];
    public $relationGrids = [];

    public function activeFieldForAttribute($attribute, $model, $params = [])
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->activeFields);
        if ($key) {
            return $this->activeFields[$key]($attribute, $model, $params);
        }
    }

    public function prependActiveFieldForAttribute($attribute, $model, $params = [])
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->prependActiveFields);
        if ($key) {
            return $this->prependActiveFields[$key]($attribute, $model, $params);
        }
    }

    public function appendActiveFieldForAttribute($attribute, $model, $params = [])
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->appendActiveFields);
        if ($key) {
            return $this->appendActiveFields[$key]($attribute, $model, $params);
        }
    }


    public function attributeFormatForAttribute($attribute, $model, $params = [])
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->attributeFormats);
        if ($key) {
            return $this->attributeFormats[$key]($attribute, $model, $params);
        }
    }

    public function columnFormatForAttribute($attribute, $model, $params = [])
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->columnFormats);
        if ($key) {
            return $this->columnFormats[$key]($attribute, $model, $params);
        }
    }

    public function relationGridForAttribute($attribute, $model, $params = [])
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->relationGrids);
        if ($key) {
            return $this->relationGrids[$key]($attribute, $model, $params);
        }
    }

    private function getModelKey($attribute, $model)
    {
        return get_class($model) . '.' . $attribute;
    }

    private function findValue($subject, $array)
    {
        foreach ($array AS $key => $value) {
            if (preg_match('/' . $key . '/', $subject)) {
                return $key;
            }
        }
    }

}