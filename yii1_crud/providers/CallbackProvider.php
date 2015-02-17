<?php

namespace neam\workflow_ui_giiant_generator\crud\providers;

class CallbackProvider extends \schmunk42\giiant\base\Provider
{
    public $activeFields = [];
    public $prependActiveFields = [];
    public $appendActiveFields = [];
    public $attributeFormats = [];
    public $columnFormats = [];

    public function activeFieldForAttribute($attribute, $model)
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->activeFields);
        if ($key) {
            return $this->activeFields[$key]($attribute, $model);
        }
    }

    public function prependActiveFieldForAttribute($attribute, $model)
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->prependActiveFields);
        if ($key) {
            return $this->prependActiveFields[$key]($attribute, $model);
        }
    }

    public function appendActiveFieldForAttribute($attribute, $model)
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->appendActiveFields);
        if ($key) {
            return $this->appendActiveFields[$key]($attribute, $model);
        }
    }


    public function attributeFormatForAttribute($attribute, $model)
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->attributeFormats);
        if ($key) {
            return $this->attributeFormats[$key]($attribute, $model);
        }
    }

    public function columnFormatForAttribute($attribute, $model)
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->columnFormats);
        if ($key) {
            return $this->columnFormats[$key]($attribute, $model);
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