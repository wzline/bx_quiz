<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Loader,
	Bitrix\Main\Entity\Query,
	Bitrix\Catalog\CatalogViewedProductTable as CVP;


if (!Loader::includeModule('catalog'))
	return false;
if (!Loader::includeModule('sale'))
	return false;

// Количество выводим просмотренных товаров
// лучше вынести в параметры компонента
$arParams['PRODUCT_COUNT'] = intval($arParams['PRODUCT_COUNT']) > 0 ? intval($arParams['PRODUCT_COUNT']) : 5;
	
// Задаем параметры выбора просмотренных товаров
$lvp = new Query(CVP::getEntity());
$lvp->setFilter(array('=FUSER_ID' => CSaleBasket::GetBasketUserID()));
$lvp->setOrder(array('DATE_VISIT' => 'DESC'));
$lvp->setLimit($arParams['PRODUCT_COUNT']);
$lvp->setSelect(array(
		'NAME' => 'ELEMENT.NAME', 
		'PRODUCT_ID', 
		'ELEMENT_ID', 
		'VIEW_COUNT', 
		'PREVIEW_PICTURE' => 'ELEMENT.PREVIEW_PICTURE',
		'DETAIL_PICTURE' => 'ELEMENT.DETAIL_PICTURE',
));
// Проверяем сгенерированный SQL-запрос перед выполнением
// echo "<pre>";print_r($lvp->getQuery());echo "</pre>";

// Получаем список просмотренных товаров
$result = $lvp->exec()->fetchAll();
// echo "<pre>";print_r($result);echo "</pre>";

// выбираем урли детальных страниц
$elements = array_column($result, 'ELEMENT_ID');
$elementURLs = array();
if (is_array($elements) && count($elements) > 0)
{
	$dbE = CIBlockElement::GetList(
		array(),
		array('ID' => $elements),
		false,
		false,
		array('ID', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'DETAIL_PICTURE')
	);
	while ($el = $dbE->GetNext(true, false))
	{
		foreach ($result as &$item)
		{
			if ($item['ELEMENT_ID'] === $el['ID'])
			{
				$item['DETAIL_PAGE_URL'] = $el['DETAIL_PAGE_URL'];
				if ($el['PREVIEW_PICTURE'])
					$item['PICTURE'] = $el['PREVIEW_PICTURE'];
				elseif ($el['DETAIL_PICTURE'])
					$item['PICTURE'] = $el['DETAIL_PICTURE'];
				else 
					$item['PICTURE'] = false;
				break;
			}
		}
	}
}

$arResult['ITEMS'] = array();
foreach ($result as $viewedEntity)
{
	$price = CCatalogProduct::GetOptimalPrice($viewedEntity['PRODUCT_ID']);
	$price['RESULT_PRICE']['PRICE_PRINT'] = CCurrencyLang::CurrencyFormat($price['RESULT_PRICE']['DISCOUNT_PRICE'], $price['RESULT_PRICE']['CURRENCY'], true);
	$arResult['ITEMS'][] = array(
		'ID' => $viewedEntity['ELEMENT_ID'],
		'PRODUCT_ID' => $viewedEntity['PRODUCT_ID'],
		'NAME' => $viewedEntity['NAME'],
		'PICTURE' => $viewedEntity['PICTURE'] ? CFile::GetFileArray($viewedEntity['PICTURE']) : false,
		'DETAIL_PAGE_URL' => $viewedEntity['DETAIL_PAGE_URL'],
		'PRICE' => $price['RESULT_PRICE'],
	);
}
// echo "<pre>";print_r([$arResult]);echo "</pre>";

$this->IncludeComponentTemplate();