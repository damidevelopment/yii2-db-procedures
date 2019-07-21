<?php

namespace damidevelopment\dbprocedures\actions;


use damidevelopment\dbprocedures\models\Resource;
use yii\base\Model;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * SimpleProcedureAction provides easy way to call procedure with input (GET or POST)
 * and return Resource containing data from procedure.
 *
 * Class SimpleProcedureAction
 * @author: Martin Štěpánek
 * @date: 03/07/2018 12:55
 * @package damidevelopment\dbprocedures\actions
 */
class SimpleProcedureAction extends ProcedureAction
{

    /**
     * @var string
     */
    public $notFoundMessage = 'Item not found';

    /**
     * @inheritdoc
     *
     * @return Resource Resource containing data from procedure
     */
    public function run()
    {
        $this->procedure->setAttributes($this->getInput());

        if (!$this->procedure->validate()) {
            return $this->procedure;
        }

        $result = $this->procedure->call();

        if(!$this->resourceClass){
            Yii::$app->response->statusCode = 204;
            return;
        }

        if (empty($result)) {
            throw new NotFoundHttpException(\Yii::t('errors', $this->notFoundMessage));
        }

        return $this->createResource($result);
    }

    /**
     * Creates resource from SimpleProcedureAction::resourceClass and loads data
     * @param array $data Data to load to resource
     * @return Resource Final loaded resource
     */
    protected function createResource(array $data): Resource
    {
        $resource = new $this->resourceClass;
        if (!($resource instanceof Resource)) {
            throw new \InvalidArgumentException('Resource must be instance of damidev\\dbprocedures\\models\\Resource.');
        }
        $resource->setAttributes($data);
        return $resource;
    }
}