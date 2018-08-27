<?php
/**
 * Поспешил и не реализовал ProductInterface
 */
use Aniart\Main\Interfaces\ProductInterface;


if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if(empty($arResult['ITEMS'])){
	return;
}
?>
<div id="recent_viewed_items" class="bx_item_list_you_looked_horizontal col5 bx_green">
	<div class="bx_item_list_title">Последние просмотренные товары:</div>
	<div class="bx_item_list_section">
		<div class="bx_item_list_slide" style="height: auto;">
			<?foreach($arResult['ITEMS'] as $product):?>
			<div class="bx_catalog_item" style="position: relative">
				<div class="">
					<a href="<?=$product['DETAIL_PAGE_URL']?>"
					   class="bx_catalog_item_images"
					   style="background-image: url('<?=$product['PICTURE']['SRC']?>')"
					   title="<?=$product['NAME']?>">
					</a>
					<div class="bx_catalog_item_title">
						<a href="<?=$product['DETAIL_PAGE_URL']?>" title="<?=$product['NAME']?>">
							<?=$product['NAME']?>
						</a>
					</div>
					<div class="bx_catalog_item_price">
						<div id="bx_1182278561_66_price" class="bx_price"><?=$product['PRICE']['PRICE_PRINT']?></div>
					</div>
				</div>
				<div class="delete-recent-item"></div>
			</div>
			<?endforeach?>
			<div style="clear: both;"></div>
		</div>
	</div>
</div>
