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
	'SeoPage' => '\Aniart\Main\Models\SeoPage',
	'Product' => '\Aniart\Main\Models\StubProduct'
]);
app()->singleton([
	'SeoParamsCollector' => '\Aniart\Main\Seo\SeoParamsCollector',
	'SeoPagesRepository' => function(\Aniart\Main\ServiceLocator $locator){
		return new Aniart\Main\Repositories\SeoPagesRepository(HL_SEO_PAGES_ID);
	},
	'SmartSeo' => function(\Aniart\Main\ServiceLocator $locator) {
		$smartSeo = \Aniart\Main\SmartSeo\SmartSeo::getInstance();
		try {
			$smartSeo->init(new \Aniart\Main\SmartSeo\Repositories\HLBlockPagesRepository());
			return $smartSeo;
		} catch (Exception $e) {
		}
	},
	'ProductsRepository' => function(\Aniart\Main\ServiceLocator $locator){
		return new \Aniart\Main\Repositories\StubProductsRepository(IB_PRODUCTS_ID);
	}
]);
//Ajax-обработчики
\Aniart\Main\Ajax\AjaxHandlerFactory::init([
	'basket' => '\Aniart\Main\Ajax\Handlers\BasketAjaxHandler',
	'recent_viewed' => '\Aniart\Main\Ajax\Handlers\RecentViewedAjaxHandler'
]);