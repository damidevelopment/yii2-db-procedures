<?php

namespace damidevelopment\dbprocedures\models;

use Yii;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\data\DataProviderInterface;
use yii\data\Pagination;

/**
 * Class ProcedureDataProvider
 * @author Jakub Hrášek
 * @date 2018-06-28 15:28:39
 * @package damidev\dbprocedures\models
 */
class ProcedureDataProvider extends Component implements DataProviderInterface
{
    /**
     * @var string|callable the column that is used as the key of the data models.
     * This can be either a column name, or a callable that returns the key value of a given data model.
     * If this is not set, the index of the [[models]] array will be used.
     * @see getKeys()
     */
    public $key;

    /**
     * @var array Resources from procedure
     */
    private $_models;

    /**
     * @var array
     */
    private $_keys;

    /**
     * @var integer Total count of rows
     */
    private $_totalCount;

    /**
     * @var false|Pagination Pagination object
     */
    private $_pagination;

    /**
     * @var IProcedure Instance of procedure
     */
    private $_procedure;

    /**
     * @var string Name of resource class
     */
    private $_resourceClass;


    /**
     * @return IProcedure
     */
    public function getProcedure(): IProcedure
    {
        return $this->_procedure;
    }

    /**
     * @param IProcedure
     * @return self
     */
    public function setProcedure(IProcedure $procedure): self
    {
        $this->_procedure = $procedure;
        return $this;
    }

    /**
     * @return string
     */
    public function getResourceClass()
    {
        return $this->_resourceClass;
    }

    /**
     * @param string $resourceClass
     * @return $this
     */
    public function setResourceClass(string $resourceClass)
    {
        $this->_resourceClass = $resourceClass;
        return $this;
    }

    /**
     * Prepares the data models and keys.
     *
     * This method will prepare the data models and keys that can be retrieved via
     * [[getModels()]] and [[getKeys()]].
     *
     * This method will be implicitly called by [[getModels()]] and [[getKeys()]] if it has not been called before.
     *
     * @param bool $forcePrepare whether to force data preparation even if it has been done before.
     */
    public function prepare($forcePrepare = false)
    {
        if ($forcePrepare || $this->_models === null) {
            $this->_models = $this->prepareModels();
        }

        if ($forcePrepare || $this->_keys === null) {
            $this->_keys = $this->prepareKeys($this->_models);
        }
    }

    /**
     * Prepares the data models that will be made available in the current page.
     * @return array the available data models
     */
    protected function prepareModels(): array
    {
        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();
            if ($pagination->totalCount === 0) {
                return [];
            }
        }
        $data = $this->getProcedure()->call();
        $result = [];
        foreach ($data as $item) {
            $object = new $this->_resourceClass();
            $object->load($item, '');
            $result[] = $object;
        }
        return $result;
    }

    /**
     * Prepares the keys associated with the currently available data models.
     * @param array $models the available data models
     * @return array the keys
     */
    protected function prepareKeys(array $models): array
    {
        if ($this->key !== null) {
            $keys = [];
            foreach ($models as $model) {
                if (is_string($this->key)) {
                    $keys[] = $model[$this->key];
                } else {
                    $keys[] = call_user_func($this->key, $model);
                }
            }

            return $keys;
        }

        return array_keys($models);
    }

    /**
     * Returns the number of data models in the current page.
     * This is equivalent to `count($provider->getModels())`.
     * When [[getPagination|pagination]] is false, this is the same as [[getTotalCount|totalCount]].
     * @return int the number of data models in the current page.
     */
    public function getCount()
    {
        return count($this->getModels());
    }

    /**
     * Returns the total number of data models.
     * When [[getPagination|pagination]] is false, this is the same as [[getCount|count]].
     * @return int total number of possible data models.
     */
    public function getTotalCount()
    {
        if ($this->getPagination() === false) {
            return $this->getCount();
        } elseif ($this->_totalCount === null) {
            $result = $this->getProcedure()->callCount();
            $this->_totalCount = intval($result);
        }

        return $this->_totalCount;
    }

    /**
     * Returns the data models in the current page.
     * @return array the list of data models in the current page.
     */
    public function getModels()
    {
        $this->prepare();

        return $this->_models;
    }

    /**
     * Returns the key values associated with the data models.
     * @return array the list of key values corresponding to [[getModels|models]]. Each data model in [[getModels|models]]
     * is uniquely identified by the corresponding key value in this array.
     */
    public function getKeys()
    {
        $this->prepare();

        return $this->_keys;
    }

    /**
     * @return bool Sort the sorting object. If this is false, it means the sorting is disabled.
     */
    public function getSort()
    {
        return false;
    }

    /**
     * Returns the pagination object used by this data provider.
     * Note that you should call [[prepare()]] or [[getModels()]] first to get correct values
     * of [[Pagination::totalCount]] and [[Pagination::pageCount]].
     * @return Pagination|false the pagination object. If this is false, it means the pagination is disabled.
     */
    public function getPagination()
    {
        if ($this->_pagination === null) {
            $this->setPagination([]);
        }

        return $this->_pagination;
    }

    /**
     * Sets the pagination for this data provider.
     * @param array|Pagination|bool $value the pagination to be used by this data provider.
     * This can be one of the following:
     *
     * - a configuration array for creating the pagination object. The "class" element defaults
     *   to 'yii\data\Pagination'
     * - an instance of [[Pagination]] or its subclass
     * - false, if pagination needs to be disabled.
     *
     * @throws InvalidArgumentException
     */
    public function setPagination($value)
    {
        if (is_array($value)) {
            $proc = $this->getProcedure();

            $this->_pagination = Yii::createObject(array_merge([
                'class' => Pagination::class,
                'params' => [
                    'page' => $proc->page,
                    'per-page' => $proc->limit
                ]
            ], $value));
        } elseif ($value instanceof Pagination || $value === false) {
            $this->_pagination = $value;
        } else {
            throw new InvalidArgumentException('Only Pagination instance, configuration array or false is allowed.');
        }
    }

}