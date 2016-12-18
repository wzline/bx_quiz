<?
namespace Aniart\Main\Ajax\Handlers;

use Aniart\Main\Ajax\AbstractAjaxHandler;
use Aniart\Main\Repositories\Credits\CreditsRepository;
use Aniart\Main\Repositories\Credits\PaySystemsCreditsRepository;

class BasketAjaxHandler extends AbstractAjaxHandler
{
    protected $lang;
    protected $productProvider = '\Aniart\Main\Services\CustomProductProvider';

    public function __construct()
    {
        $this->lang = i18n()->lang();
        parent::__construct();
    }

    protected function getFunction()
    {
        return $this->request['func'];
    }

    public function add($productId, $customArProductParams = array())
    {
	    global $APPLICATION;
        $productId = (int)$productId ? (int)$productId : (int)$_REQUEST['product-id'];
        if (!$productId)
            die('Not enough data');

        $catalogElementRepo = app('CatalogElementRepository');
        $product = $catalogElementRepo->getProduct($productId);

        if (!($product instanceof \Aniart\Main\Catalog\Models\Product))
            die('Not a true type of the object');

        $arProductParams = array(
            array(
                'NAME' => i18n('PRODUCT_CODE_1C', 'BASKET'),
                'CODE' => 'CML2_ARTICLE',
                'VALUE' => $product->getCode1c()
            )
        );

        if (!empty($customArProductParams))
            $arProductParams = array_merge($arProductParams, $customArProductParams);


        if (($id = Add2BasketByProductID($productId, 1, array('PRODUCT_PROVIDER_CLASS' => $this->productProvider), $arProductParams)) !== false) {
            $this->updateBonusProps($id, $product);
            $result = array("status" => 1);
        } else {
	        $result = array("status" => 0);
	        if($e = $APPLICATION->GetException()){
				$result['message'] = $e->GetString();
	        }
        }

        $this->setOK($result);
    }

    protected function updateBonusProps($basketItemId, $product)
    {
        if (!$basketItemId || !$product)
            return false;

        $basketItem = \CSaleBasket::GetByID($basketItemId);

        $dbRes = \CSaleBasket::GetPropsList(array(), array("BASKET_ID" => $basketItemId));
        $curProps = array();
        while ($arProp = $dbRes->Fetch()) {
            $curProps[$arProp['CODE']] = $arProp;
        }

        foreach ($curProps as $prop) {
            if ($prop['CODE'] == 'BONUS_PERCENT' || $prop['CODE'] == 'ITEM_BONUS_PRICE' || $prop['CODE'] == 'SUM_BONUS_PRICE')
                continue;

            $props[] = array(
                'NAME' => $prop['NAME'],
                'CODE' => $prop['CODE'],
                'VALUE' => $prop['VALUE']
            );
        }
        $props = array_merge($props, array(
            array(
                'NAME' => i18n('BONUS_PERCENT_BY_MINIMAL_PRICE', 'BASKET'),
                'CODE' => 'BONUS_PERCENT',
                'VALUE' => $product->getBonusPercent()
            ),
            array(
                'NAME' => i18n('ITEM_SUM_BONUS', 'BASKET'),
                'CODE' => 'ITEM_BONUS_PRICE',
                'VALUE' => $product->getBonusPrice()
            ),
            array(
                'NAME' => i18n('TOTAL_SUM_BONUS', 'BASKET'),
                'CODE' => 'SUM_BONUS_PRICE',
                'VALUE' => $product->getBonusPrice() * $basketItem['QUANTITY']
            )
        ));

        $arFields = array(
            'QUANTITY' => $basketItem['QUANTITY'],
            'PROPS' => $props);

        \CSaleBasket::_Update($basketItemId, $arFields);
    }

    public function buyProductOnCredit()
    {
        $productId  = (int)$_REQUEST['product-id'] ? (int)$_REQUEST['product-id'] : false;
        $bankId     = (int)$_REQUEST['bank-id'] ? (int)$_REQUEST['bank-id'] : false;
        $period     = (int)$_REQUEST['period'] ? (int)$_REQUEST['period'] : false;
        $creditType = trim($this->post['credit-type']);

        if (!$productId || !$bankId || !$period || !$creditType) {
            if (!$bankId) {
                $this->setOK(array("status" => 0, "bankId" => 'empty'));
                return;
            }
            else {
                die('Not enough data');
            }
        }


        $catalogElementRepo = app('CatalogElementRepository');
        $product = $catalogElementRepo->getProduct($productId);

        if (!($product instanceof \Aniart\Main\Catalog\Models\Product)) {
            die('Not a true type of the object');
        }

        /**
         * @var PaySystemsCreditsRepository $creditsRepository
         */
        if($creditType == 'application'){
            $creditsRepository = app('ApplicationsCreditsRepository');
        }
        else{
            $creditsRepository = app('CreditsRepository');
        }

        $credits = $creditsRepository->getArrayListGroupedByPeriod([], ['ACTIVE' => 'Y']);
        $creditInfo = $credits[$period][$bankId];

        if(!empty($creditInfo)){
            $paySystemId = $creditInfo['PAY_SYSTEMS'][$period];
            app('OrdersService')->setPotentialOrderPaySystem($paySystemId);
        }

        $this->add($productId);
    }


		public function updateBasketProduct()
    {
					$productId = $this->request['product_id'] ? $this->request['product_id'] : false;
        	$warranty = $this->request['warranty'] ? $this->request['warranty'] : false;
					$basketItemId = (int)$this->request['id'] ? $this->request['id'] : false;
					$quantity = $this->request['quantity'] ? $this->request['quantity'] : 0;
					
					
					// получаем кол-во на всех складах
					$tempQuntity = [];
					$arResProduct = \CCatalogProduct::GetByID($productId);
					if($quantity > $arResProduct['QUANTITY'])
					{
						$tempQuntity["ID"] = $basketItemId;
						$tempQuntity['MAX'] = $arResProduct['QUANTITY'];
						$tempQuntity['FULL'] = $quantity;
						
						$quantity = $arResProduct['QUANTITY'];
					}
					
					
					$arFields['QUANTITY'] = $quantity;
			
            if (\CModule::IncludeModule("sale") || \CModule::IncludeModule("catalog") ){            	
                $dbRes = \CSaleBasket::GetPropsList(array(), array("BASKET_ID" => $basketItemId));
                $curProps = array();
                while ($arProp = $dbRes->Fetch()) {
                	$curProps[$arProp['CODE']] = $arProp;
                }
                
                $warrantyFlag = false;
                foreach ($curProps as $prop) {
                	$value = $prop['VALUE'];
                	if ($prop['CODE'] == 'SUM_WARRANTY_PRICE' || $prop['CODE'] == 'ITEM_WARRANTY_PRICE') {
                		$warrantyFlag = true;
                		
                		if ($warranty == 'unchecked')
                			continue;
                		
                		if ($prop['CODE'] == 'SUM_WARRANTY_PRICE')
                			$value = $curProps['ITEM_WARRANTY_PRICE']['VALUE'] * $quantity;                			
                	}                	
                	if ($prop['CODE'] == 'SUM_BONUS_PRICE') 
                		$value = $curProps['ITEM_BONUS_PRICE']['VALUE'] * $quantity;
                	
                	$props[] = array(
                			'NAME' => $prop['NAME'],
                			'CODE' => $prop['CODE'],
                			'VALUE' => $value
                	);
                }

                $arFields = array_merge($arFields, array('PROPS' => $props));
                
                if (!$warrantyFlag && $productId && $warranty == 'checked') {                	
                		$dbResElem = \CIBlockElement::GetList(array(), array('IBLOCK_ID' => CATALOG_IBLOCK_ID, 'ACTIVE' => 'Y', 'ID' => $productId), false, false, array('*'));
                		if ($arElement = $dbResElem->GetNext()) {
                			$catalogElementRepo  = app('CatalogElementRepository');
                			$element = $catalogElementRepo->newInstance($arElement);
                		}
                		
                		$warrantyPrice = $element->getWarrantyPrice();
                		$sumWarrantyPrice = $warrantyPrice * $quantity;
                		$props[] = array(
                				'NAME' => 'Расчитанная сумма за гарантию для единицы товара',
                				'CODE' => 'ITEM_WARRANTY_PRICE',
                				'VALUE' => $warrantyPrice);
                		$props[] = array(
                				'NAME' => 'Итоговая сумма за гарантию с учетом количества товара',
                				'CODE' => 'SUM_WARRANTY_PRICE',
                				'VALUE' => $sumWarrantyPrice);
                		
                		$arFields = array_merge($arFields, array(
                				'PRICE' => $element->getMinPrice() + $warrantyPrice,
                				'CUSTOM_PRICE' => 'Y',
                				'PROPS' => $props));        		
                }             
				
                $arFields['CAN_BUY'] = 'Y';
                   
                if (!$quantity)
                	\CSaleBasket::Update($basketItemId, $arFields);
                else{
                	\CSaleBasket::_Update($basketItemId, $arFields);
                }
								$result = array($tempQuntity);
        				$this->setOK($result);
          }
    }


    //все, что ниже - не доделано
    public function update()
    {
        $productId = $this->request['product_id'] ? (int)$this->request['product_id'] : false;
        $warranty = $this->request['warranty'] ? $this->request['warranty'] : false;
        $basketItemId = (int)$this->request['id'];
        $quantity = (int)$this->request['quantity'];
        $quantity = $quantity ? $quantity : 0;

        if (!$basketItemId) {
            $html = $this->getBasketComponent();
            $this->setOK(array('html' => $html));
            die();
        }

        $arFields['QUANTITY'] = $quantity;

        $dbRes = \CSaleBasket::GetPropsList(array(), array("BASKET_ID" => $basketItemId));
        $curProps = array();
        while ($arProp = $dbRes->Fetch()) {
            $curProps[$arProp['CODE']] = $arProp;
        }

        $warrantyFlag = false;
        foreach ($curProps as $prop) {
            $value = $prop['VALUE'];
            if ($prop['CODE'] == 'SUM_WARRANTY_PRICE' || $prop['CODE'] == 'ITEM_WARRANTY_PRICE') {
                $warrantyFlag = true;

                if ($warranty == 'unchecked')
                    continue;

                if ($prop['CODE'] == 'SUM_WARRANTY_PRICE')
                    $value = $curProps['ITEM_WARRANTY_PRICE']['VALUE'] * $quantity;
            }
            if ($prop['CODE'] == 'SUM_BONUS_PRICE')
                $value = $curProps['ITEM_BONUS_PRICE']['VALUE'] * $quantity;

            $props[] = array(
                'NAME' => $prop['NAME'],
                'CODE' => $prop['CODE'],
                'VALUE' => $value
            );
        }

        $arFields = array_merge($arFields, array('PROPS' => $props));

        if (!$warrantyFlag && $productId && $warranty == 'checked') {
            $dbResElem = \CIBlockElement::GetList(array(), array('IBLOCK_ID' => CATALOG_IBLOCK_ID, 'ACTIVE' => 'Y', 'ID' => $productId), false, false, array('*'));
            if ($arElement = $dbResElem->GetNext()) {
                $catalogElementRepo = app('CatalogElementRepository');
                $element = $catalogElementRepo->newInstance($arElement);
            }

            $warrantyPrice = $element->getWarrantyPrice();
            $sumWarrantyPrice = $warrantyPrice * $quantity;
            $props[] = array(
                'NAME' => 'Расчитанная сумма за гарантию для единицы товара',
                'CODE' => 'ITEM_WARRANTY_PRICE',
                'VALUE' => $warrantyPrice);
            $props[] = array(
                'NAME' => 'Итоговая сумма за гарантию с учетом количества товара',
                'CODE' => 'SUM_WARRANTY_PRICE',
                'VALUE' => $sumWarrantyPrice);

            $arFields = array_merge($arFields, array(
                'PRICE' => $element->getMinPrice() + $warrantyPrice,
                'CUSTOM_PRICE' => 'Y',
                'PROPS' => $props));
        }
        $arFields['CAN_BUY'] = 'Y';

        if (!$quantity)
            \CSaleBasket::Update($basketItemId, $arFields);
        else
            \CSaleBasket::_Update($basketItemId, $arFields);

        include($includePath . 'sale-basket.php');

    }

    protected function getBasketComponent()
    {
        ob_start();
        $APPLICATION->IncludeComponent(
            "bitrix:sale.basket.basket",
            "basket-ajax",
            Array(
                "ACTION_VARIABLE" => "action",
                "COLUMNS_LIST" => array(
                    0 => "NAME",
                    1 => "QUANTITY",
                    2 => "PROPS",
                    3 => "DELETE",
                    4 => "PRICE",
                    5 => "SUM",
                    6 => "BONUS"
                ),
                "COMPONENT_TEMPLATE" => ".default",
                "COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
                "GIFTS_BLOCK_TITLE" => "Выберите один из подарков",
                "GIFTS_CONVERT_CURRENCY" => "Y",
                "GIFTS_HIDE_BLOCK_TITLE" => "N",
                "GIFTS_HIDE_NOT_AVAILABLE" => "N",
                "GIFTS_MESS_BTN_BUY" => "Выбрать",
                "GIFTS_MESS_BTN_DETAIL" => "Подробнее",
                "GIFTS_PAGE_ELEMENT_COUNT" => "4",
                "GIFTS_PRODUCT_PROPS_VARIABLE" => "prop",
                "GIFTS_PRODUCT_QUANTITY_VARIABLE" => "",
                "GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
                "GIFTS_SHOW_IMAGE" => "Y",
                "GIFTS_SHOW_NAME" => "Y",
                "GIFTS_SHOW_OLD_PRICE" => "Y",
                "GIFTS_TEXT_LABEL_GIFT" => "Подарок",
                "HIDE_COUPON" => "N",
                "OFFERS_PROPS" => array("CML2_ARTICLE"),
                "PATH_TO_ORDER" => i18n()->getLangDir('/order/'),
                "PRICE_VAT_SHOW_VALUE" => "N",
                "QUANTITY_FLOAT" => "N",
                "SET_TITLE" => "Y",
                "TEMPLATE_THEME" => "blue",
                "USE_GIFTS" => "Y",
                "USE_PREPAYMENT" => "N"
            )
        );
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function checkBasket()
    {
        $basketRepo = app('BasketItemRepository');
        $arBasket = $basketRepo->getList([], ["FUSER_ID" => \CSaleBasket::GetBasketUserID(), "ORDER_ID" => "NULL"]);
        if (empty($arBasket))
            $result = array("status" => 0);
        else
            $result = array("status" => 1);
        $this->setOK($result);
    }

    public function deleteUserBasket()
    {
        $basketRepo = app('BasketItemRepository');
        $arBasket = $basketRepo->getList([], ["FUSER_ID" => \CSaleBasket::GetBasketUserID(), "ORDER_ID" => "NULL"]);
        foreach ($arBasket as $key => $basket) {
            if ($basketRepo->deleteForCurrentUser($basket->getId())) {
                unset($arBasket[$key]);
            }
        }
        if (empty($arBasket))
            $result = array("status" => 1);
        else
            $result = array("status" => 1);
        $this->setOK($result);
    }
}