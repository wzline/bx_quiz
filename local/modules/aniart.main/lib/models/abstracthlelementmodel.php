<?php


namespace Aniart\Main\Models;


class AbstractHLElementModel extends AbstractModel
{
    protected static $userFields;

    public function getId()
    {
        return $this->ID;
    }

    public function isNew()
    {
        return $this->getId() <= 0;
    }

    public function getHLBlockId()
    {
        return $this->HLBLOCK_ID;
    }
    
    public function getHighloadUserFieldsObject()
    {
        return 'HLBLOCK_'.$this->getHLBlockId();
    }

    public function obtainUserFields()
    {
        global $USER_FIELD_MANAGER;
        static::$userFields = $USER_FIELD_MANAGER->GetUserFields(
            $this->getHighloadUserFieldsObject()
        );
    }

    public function getUserFields()
    {
        if(is_null(static::$userFields)){
            $this->obtainUserFields();
        }
        return static::$userFields;
    }

    public function getUserField($ufCode, $ufField = '')
    {
        $uf = $this->getUserFields();
        $uf = $uf[$ufCode];
        if(!empty($ufField)){
            $uf = $uf[$ufField];
        }

        return $uf;
    }

}