<?php

namespace damidev\dbprocedures\utils;

use yii\base\Arrayable;
use yii\helpers\ArrayHelper;


class Serializer extends \yii\rest\Serializer
{

    /**
     * Serializes the validation errors in a model.
     * @param Model $model
     * @return array the array representation of the errors
     */
    protected function serializeModelErrors($model)
    {
        $this->response->setStatusCode(422, 'Data Validation Failed');

        $result = [];
        foreach ($model->getErrors() as $name => $message) {
            $result[] = [
                'field' => $name,
                'messages' => $message,
            ];
        }

        return [
            'code' => 422,
            'errorName' => 'Data Validation Failed',
            'message' => 'Data Validation Failed',
            'errors' => $result
        ];
    }

    /**
     * Serializes a set of models.
     * @param array $models
     * @return array the array representation of the models
     */
    protected function serializeModels(array $models)
    {
        list($fields, $expand) = $this->getRequestedFields();
        foreach ($models as $i => $model) {
            if ($model instanceof Arrayable) {
                $models[$i] = $model->toArray($fields, $expand);
            }
            elseif (is_array($model)) {
                $models[$i] = ArrayHelper::toArray($model);
            }
            else {
                $models[$i] = $model;
            }
        }

        return $models;
    }
}
