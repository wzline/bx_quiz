<?php

namespace Aniart\Main\Repositories;

use Aniart\Main\Models\AbstractModel;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity\DataManager;

abstract class AbstractHLBlockElementsRepository
{
    /**
     * @var DataManager
     */
    protected static $entities;
    protected $hlblock_id;

    public function __construct($hlblock_id)
    {
        if(!static::$entities[$hlblock_id]){
            $hlblock = HighloadBlockTable::getById($hlblock_id)->fetch();
            static::$entities[$hlblock_id] = HighloadBlockTable::compileEntity($hlblock)->getDataClass();
            $this->hlblock_id = $hlblock_id;
        }
    }

    abstract public function newInstance(array $fields);

    public function getHLBlockId()
    {
        return $this->hlblock_id;
    }

    public function getEntity()
    {
        return static::$entities[$this->hlblock_id];
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

    public function getList($arOrder = array('ID' => 'ASC'), $arFilter = array(), $arGroup = false, $arNavStartParams = false, $arSelect = array())
    {
        $result = array();
        $queryParams = $this->createQueryParams($arOrder, $arFilter, $arGroup, $arNavStartParams, $arSelect);
        $entity = $this->getEntity();
        $rsData = $entity::getList($queryParams);
        while($arData = $rsData->Fetch()){
            $result[] = $this->newInstance($arData);
        }

        return $result;
    }

    public function getById($id)
    {
        $id = (int)$id;
        $result = $this->getList(array('ID' => 'ASC'), array('ID' => $id));
        if(!empty($result)){
            return $result[0];
        }
        return false;
    }

    public function add(array $fields = array())
    {
        $entity = $this->getEntity();
        return $entity::add($fields);
    }

    public function update($id, array $fields = array())
    {
        $entity = $this->getEntity();
        return $entity::update($id, $fields);
    }

    public function save(AbstractModel $model)
    {
        $id = $model->getId();
        if($id > 0){
            $result = $this->update($id, $model->toArray());
        }
        else{
            $result = $this->add($model->toArray());
            if($result->isSuccess()) {
                $model->ID = $result->getId();
            }
        }
        return $result;
    }
}
?>