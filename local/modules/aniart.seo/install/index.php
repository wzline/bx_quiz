<?php

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\ModuleManager,
	Bitrix\Main\Loader,
	Bitrix\Highloadblock\HighloadBlockTable as HBT;

Loc::loadMessages(__FILE__);
if (class_exists('aniart_seo')) {
	return;
}
class aniart_seo extends CModule 
{
	public $MODULE_ID = 'aniart.seo';
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $MODULE_GROUP_RIGHTS = 'N';
	public $PARTNER_NAME;
	public $PARTNER_URI;
	
	protected static $hbName = 'AniartSeo';
	protected static $hbTableName = 'aniart_seo';

	function __construct()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}
		
		$this->MODULE_NAME = Loc::getMessage('ANIART_SEO_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('ANIART_SEO_MODULE_DESCRIPTION');
		$this->PARTNER_NAME = Loc::getMessage('ANIART_SEO_PARTNER_NAME');
		$this->PARTNER_URI = Loc::getMessage('ANIART_SEO_PARTNER_URI');
	}
	
	public function DoInstall()
	{
		global $APPLICATION;
		if (ModuleManager::isModuleInstalled('aniart.main'))
		{
			$r = self::createHL();
			if ($r)
				RegisterModule($this->MODULE_ID);
			else 
				return false;
		}
		else 
		{
			$APPLICATION->ThrowException(Loc::getMessage('ANIART_SEO_NEED_ANIART_MAIN'));
			return false;
		}
	}
	public function DoUninstall()
	{
		global $APPLICATION;
		if (self::deleteHL())
			UnRegisterModule($this->MODULE_ID);
		else
			return false;
	}
	
	/**
	 * Метод создает Highload-инфоблок и его поля
	 * @return boolean
	 */
	protected function createHL()
	{
		global $APPLICATION;
		// Проверяем подключен ли модуль highloadblock
		if (!Loader::includeModule('highloadblock'))
		{
			// Если модуль не подключен, то прерываем установку модуля и сообщаяем об ошибке
			$APPLICATION->ThrowException(Loc::getMessage('ANIART_SEO_MODULE_HL_NOT_INSTALLED'));
			return false;
		}
		
		// Проверим существует ли наш hl-блок
		$params = array(
			'filter' => array(
				'=NAME' => self::$hbName,
				'=TABLE_NAME' => self::$hbTableName,
			),
			'select' => array('ID')
		);
		$hl = HBT::getList($params)->fetch();
		// Если hl-блок не найден, создадим
		if (!$hl)
		{
			$res = HBT::add(array(
				'NAME' => self::$hbName,
				'TABLE_NAME' => self::$hbTableName
			));
			
			// Если hl-блок не создался, обработаем ошибку и прервем установку
			if (!$res->isSuccess())
			{
				$APPLICATION->ThrowException($res->getErrorMessages());
				return false;
			}
			
			// Добавим полей нашему HL-блоку
			$hlId = $res->getId();
			if ($fields = self::getFields4HL($hlId))
			{
				$oUserTypeEntity = new CUserTypeEntity();
				foreach ($fields as $field)
				{
					$idUserTypeEntity = $oUserTypeEntity->Add($field);
					if (!$idUserTypeEntity)
						return false;
				}
			}
			return true;
		}
		// Если найден, то считаем что всё ОК
		else 
		{
			return true;
		}
	}

	/**
	 * Метод удаляет HL-блок
	 * заглущка
	 */
	protected function deleteHL()
	{
		return true;
	}
	/**
	 * Метод возвращает описание полей HL-блока
	 * @param int $id
	 */
	protected function getFields4HL($id)
	{
		if (intval($id) <= 0)
			return false;

		return array(
			array(
				'ENTITY_ID' => 'HLBLOCK_' . $id,
				'FIELD_NAME' => 'UF_PAGE',
				'USER_TYPE_ID' => 'string',
				'SORT' => 100,
				'MULTIPLE' => 'N',
				'MANDATORY' => 'Y',
				'SHOW_FILTER' => 'Y',
				'SHOW_IN_LIST' => 'Y',
				'EDIT_IN_LIST' => 'Y',
				'IS_SEARCHABLE' => 'N',
				'SETTINGS' => array(),	
			),
			array(
				'ENTITY_ID' => 'HLBLOCK_' . $id,
				'FIELD_NAME' => 'UF_PAGE_TITLE',
				'USER_TYPE_ID' => 'string',
				'SORT' => 200,
				'MULTIPLE' => 'N',
				'MANDATORY' => 'Y',
				'SHOW_FILTER' => 'Y',
				'SHOW_IN_LIST' => 'Y',
				'EDIT_IN_LIST' => 'Y',
				'IS_SEARCHABLE' => 'N',
				'SETTINGS' => array(),	
			),
			array(
				'ENTITY_ID' => 'HLBLOCK_' . $id,
				'FIELD_NAME' => 'UF_SORT',
				'USER_TYPE_ID' => 'integer',
				'SORT' => 300,
				'MULTIPLE' => 'N',
				'MANDATORY' => 'Y',
				'SHOW_FILTER' => 'Y',
				'SHOW_IN_LIST' => 'Y',
				'EDIT_IN_LIST' => 'Y',
				'IS_SEARCHABLE' => 'N',
				'SETTINGS' => array(),	
			),
			array(
				'ENTITY_ID' => 'HLBLOCK_' . $id,
				'FIELD_NAME' => 'UF_META_TITLE',
				'USER_TYPE_ID' => 'string',
				'SORT' => 400,
				'MULTIPLE' => 'N',
				'MANDATORY' => 'Y',
				'SHOW_FILTER' => 'Y',
				'SHOW_IN_LIST' => 'Y',
				'EDIT_IN_LIST' => 'Y',
				'IS_SEARCHABLE' => 'N',
				'SETTINGS' => array(),	
			),
			array(
				'ENTITY_ID' => 'HLBLOCK_' . $id,
				'FIELD_NAME' => 'UF_KEYWORDS',
				'USER_TYPE_ID' => 'string',
				'SORT' => 500,
				'MULTIPLE' => 'N',
				'MANDATORY' => 'Y',
				'SHOW_FILTER' => 'Y',
				'SHOW_IN_LIST' => 'Y',
				'EDIT_IN_LIST' => 'Y',
				'IS_SEARCHABLE' => 'N',
				'SETTINGS' => array(),	
			),
			array(
				'ENTITY_ID' => 'HLBLOCK_' . $id,
				'FIELD_NAME' => 'UF_DESCRIPTION',
				'USER_TYPE_ID' => 'string',
				'SORT' => 600,
				'MULTIPLE' => 'N',
				'MANDATORY' => 'Y',
				'SHOW_FILTER' => 'Y',
				'SHOW_IN_LIST' => 'Y',
				'EDIT_IN_LIST' => 'Y',
				'IS_SEARCHABLE' => 'N',
				'SETTINGS' => array(),	
			),
		);
	}
}  
?>