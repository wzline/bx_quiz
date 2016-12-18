<?php
/**
 *Точка входа модуля aniart.main
 */

$modulePath = dirname(__FILE__);
include $modulePath.'/vars.php';
include $modulePath.'/utils.php';
include $modulePath.'/misc.php';
include $modulePath.'/events.php';

//Подключаем зависимые модули
Bitrix\Main\Loader::includeModule('highloadblock');
Bitrix\Main\Loader::includeModule('iblock');
Bitrix\Main\Loader::includeModule('catalog');
Bitrix\Main\Loader::includeModule('sale');

app()->bind([
	'SeoPage' => '\Aniart\Main\Models\SeoPage'
]);
app()->singleton([
	'SeoParamsCollector' => '\Aniart\Main\Seo\SeoParamsCollector',
	'SeoPagesRepository' => function(\Aniart\Main\ServiceLocator $locator){
		return new Aniart\Main\Repositories\SeoPagesRepository(HL_SEO_PAGES_ID);
	},
	'SmartSeo' => function(\Aniart\Main\ServiceLocator $locator){
		$smartSeo = \Aniart\Main\SmartSeo\SmartSeo::getInstance();
		try {
			$smartSeo->init(new \Aniart\Main\SmartSeo\Repositories\HLBlockPagesRepository());
			return $smartSeo;
		}
		catch (Exception $e){}
	}
]);

//Ajax-обработчики
\Aniart\Main\Ajax\AjaxHandlerFactory::init([]);