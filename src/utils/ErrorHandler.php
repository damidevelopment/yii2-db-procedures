<?php

namespace damidev\dbprocedures\utils;

use app\api\ValidationException;
use app\api\MinimumAppVersionException;
use Yii;
use yii\base\Exception;
use yii\base\UserException;
use yii\db\Exception as DbException;
use yii\web\HttpException;


/**
 * @Author: Jakub Hrášek
 * @Date:   2018-06-22 11:08:15
 */
class ErrorHandler extends \yii\web\ErrorHandler
{
    /**
     * Converts an exception into an array.
     * @param \Exception|\Error $exception the exception being converted
     * @return array the array representation of the exception.
     */
    protected function convertExceptionToArray($exception)
    {
        if (!YII_DEBUG && !$exception instanceof UserException && !$exception instanceof HttpException) {
            $exception = new HttpException(500, Yii::t('errors', 'An internal server error occurred'));
        }

        $array = [
            'errorName' => Yii::t('errors', ($exception instanceof Exception) ? $exception->getName() : 'Exception'),
            'message' => Yii::t('errors', $exception->getMessage()),
            'code' => $exception->getCode(),
        ];

        if ($exception instanceof HttpException) {
            $array['code'] = $exception->statusCode;
        }

        if ($exception instanceof ValidationException) {
            $array['errors'] = [];
            foreach ($exception->model->getErrors() as $name => $message) {
                $array['errors'][] = [
                    'field' => $name,
                    'messages' => $message,
                ];
            }
        }

        if (YII_DEBUG) {
            $array['type'] = get_class($exception);
            if (!$exception instanceof UserException) {
                $array['file'] = $exception->getFile();
                $array['line'] = $exception->getLine();
                $array['stack-trace'] = explode("\n", $exception->getTraceAsString());
                if ($exception instanceof DbException) {
                    $array['error-info'] = $exception->errorInfo;
                }
            }
        }

        if (($prev = $exception->getPrevious()) !== null) {
            $array['previous'] = $this->convertExceptionToArray($prev);
        }

        return $array;
    }

}