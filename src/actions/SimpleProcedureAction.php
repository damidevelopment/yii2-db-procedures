<?php

namespace damidev\dbprocedures\actions;


use damidev\dbprocedures\models\Resource;
use yii\base\Model;
use yii\web\NotFoundHttpException;

/**
 * SimpleProcedureAction provides easy way to call procedure with input (GET or POST)
 * and return Resource containing data from procedure.
 *
 * Class SimpleProcedureAction
 * @author: Martin Štěpánek
 * @date: 03/07/2018 12:55
 * @package damidev\dbprocedures\actions
 */
class SimpleProcedureAction extends ProcedureAction
{
    /**
     * @inheritdoc
     *
     * @return Resource Resource containing data from procedure
     */
    public function run()
    {
        $this->procedure->load($this->getInput(), '');

        if (!$this->procedure->validate()) {
            return $this->procedure;
        }

        $result = $this->procedure->call();

        if (empty($result)) {
            throw new NotFoundHttpException(\Yii::t('errors', 'Item not found'));
        }

        return $this->createResource($result);
    }

    /**
     * Create resource from SimpleProcedureAction::resourceClass and load data
     * @param array $data Data to load to resource
     * @return Resource Final loaded resource
     */
    protected function createResource(array $data): Resource
    {
        $resource = new $this->resourceClass;
        if (!($resource instanceof Resource)) {
            throw new \InvalidArgumentException('Resource must be instance of damidev\\dbprocedures\\models\\Resource.');
        }
        $resource->load($data, '');
        return $resource;
    }
}