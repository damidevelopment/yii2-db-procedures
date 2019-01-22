<?php
/**
 * @Author: Martin Štěpánek
 * @Date: 03/07/2018 12:55
 */

namespace damidev\dbprocedures\actions;


use damidev\dbprocedures\models\Resource;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class SimpleProcedureAction extends ProcedureAction
{
    /**
     * @inheritdoc
     *
     * @return Resource
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
     * @param array $data
     * @return Resource
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