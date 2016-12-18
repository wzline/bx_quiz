<?php


namespace Aniart\Main\Repositories;

use Aniart\Main\DiscountsKits\Models\DiscountKit;
use Aniart\Main\Interfaces\ErrorableInterface;
use Aniart\Main\Models\AbstractModel;
use Aniart\Main\Traits\ErrorTrait;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Error;
use Bitrix\Main\Result;

abstract class AbstractEntitiesRepository implements ErrorableInterface
{
    use ErrorTrait;

    protected $entityClass;
    protected $primaryField;

    abstract public function newInstance(array $fields = array());

    public function __construct($entityClass, $primaryField = 'ID')
    {
        $this->entityClass  = $entityClass;
        $this->primaryField = $primaryField;
    }

    /**
     * @return DataManager
     */
    public function getEntity()
    {
        return $this->entityClass;
    }

    public function getPrimaryField()
    {
        return $this->primaryField;
    }

    public function getById($id)
    {
        $id = (int)$id;
        $result = $this->getList(array($this->getPrimaryField() => 'ASC'), array($this->getPrimaryField() => $id));
        if(!empty($result)){
            return current($result);
        }
        return false;
    }

    /**
     * @param array $arOrder
     * @param array $arFilter
     * @param bool|false $arGroup
     * @param bool|false $arNavStartParams
     * @param array $arSelect
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getList($arOrder = array(), $arFilter = array(), $arGroup = false,
                            $arNavStartParams = false, $arSelect = array())
    {
        $result = array();
        if(empty($arOrder)){
            $arOrder = array($this->getPrimaryField() => 'ASC');
        }
        $queryParams = $this->createQueryParams($arOrder, $arFilter, $arGroup, $arNavStartParams, $arSelect);
        $entity = $this->getEntity();
        $rsData = $entity::getList($queryParams);
        while($arData = $rsData->Fetch()){
            $result[$arData[$this->getPrimaryField()]] = $this->newInstance($arData);
        }

        return $result;
    }

    protected function createQueryParams($arOrder, $arFilter, $arGroup, $arNavStartParams, $arSelect)
    {
        $result = array();
        if(is_array($arOrder) && !empty($arOrder)){
            $result['order'] = $arOrder;
        }
        if(is_array($arFilter) && !empty($arFilter)){
            $result['filter'] = $arFilter;
        }
        if(is_array($arGroup) && !empty($arGroup)){
            $result['group'] = $arGroup;
        }
        if(($limit = (int)$arNavStartParams['limit']) > 0){
            $result['limit'] = $limit;
        }
        if(($offset = (int)$arNavStartParams['offset']) > 0){
            $result['offset'] = $offset;
        }
        if(is_array($arSelect) && !empty($arSelect)){
            $result['select'] = $arSelect;
        }

        return $result;
    }

    public function add(array $fields = array())
    {
        return $this->save(
            $this->newInstance($fields)
        );
    }

    public function update($id, array $fields = array())
    {
        $fields['ID'] = $id;
        return $this->save(
            $this->newInstance($fields)
        );
    }

    public function save(AbstractModel $model)
    {
        $entity = $this->getEntity();
        if($model->isNew()){
            $result = $entity::add($model->toArray());
            if($result->isSuccess()){
                $model->ID = $result->getId();
            }
        }
        else{
            $result = $entity::update($model->getId(), $model->toArray());
        }
        return $this->processBitrixResult($result);
    }

    public function delete($id)
    {
        $entity = $this->getEntity();
        return $entity::delete($id)->isSuccess();
    }

    private function processBitrixResult(Result $result)
    {
        $this->clearErrors();
        $status = $result->isSuccess();
        if(!$status){
            foreach($result->getErrorCollection() as $error){
	            /**
	             * @var Error $error
	             */
                $this->addError($error->getMessage(),$error->getCode());
            }
        }
        return $status;
    }
}