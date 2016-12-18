<?php
namespace Aniart\Main\Repositories;


use Aniart\Main\Interfaces\ErrorableInterface;
use Aniart\Main\Traits\ErrorTrait;

abstract class AbstractIblockSectionsRepository implements ErrorableInterface
{
    use ErrorTrait;

    protected $iblockId;
    protected $selectedFields = array();

    public function __construct($iblockId)
    {
        $this->iblockId = $iblockId;
    }

    abstract public function newInstance(array $fields = array());

    public function getList($arOrder = array("SORT"=>"ASC"), $arFilter = array(), $bIncCnt = false,
                            $arSelect = array(), $arNavStartParams = false)
    {
        $result = array();
        $arFilter['IBLOCK_ID'] = $this->iblockId;
        $arSelect = array_merge($this->selectedFields, $arSelect);
        $rsSections = \CIBlockSection::GetList($arOrder, $arFilter, $bIncCnt, $arSelect, $arNavStartParams);
        while($arSection = $rsSections->GetNext()){
            $result[] = $this->newInstance($arSection);
        }

        return $result;
    }

    public function getById($id)
    {
        $list = $this->getList(array(), array('ID' => $id));
        if(!empty($list)){
            return $list[0];
        }
        return false;
    }

    public function getByCode($code)
    {
        $list = $this->getList(array(), array('CODE' => $code));
        if(!empty($list)){ //считаем, что код такой же уникальный как и id
            return $list[0];
        }
        return false;
    }

    public function getByElementId($elementId, $arSelect = array())
    {
        $result  = array();
        $elementId = (int)$elementId;
        if($elementId > 0){
            $rsSections = \CIBlockElement::GetElementGroups($elementId, false, $arSelect);
            while($arSection = $rsSections->GetNext()){
                $result[] = $this->newInstance($arSection);
            }
        }
        return $result;
    }
}
?>