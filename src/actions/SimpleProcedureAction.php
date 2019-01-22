<?php
/**
 * @Author: Martin Štěpánek
 * @Date: 03/07/2018 12:55
 */

namespace damidev\dbprocedures\actions;


use app\models\ProcedureInterface;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class SimpleProcedureAction extends ProcedureAction
{
    /**
     * @inheritdoc
     *
     * @return ProcedureInterface|array
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
     */
    protected function createResource($data)
    {
        $resource = new $this->resourceClass;
        if (!($resource instanceof Model)) {
            throw new \InvalidArgumentException('Resource must be instance of yii\\base\\Model.');
        }
        $resource->load($data, '');
        return $resource;
    }
}