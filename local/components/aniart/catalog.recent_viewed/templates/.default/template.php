<?php
use Aniart\Main\Interfaces\ProductInterface;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if(empty($arResult['ELEMENTS'])){
	return;
}
?>
<div id="recent_viewed_items" class="bx_item_list_you_looked_horizontal col5 bx_green">
	<div class="bx_item_list_title">Последние просмотренные товары:</div>
	<div class="bx_item_list_section">
		<div class="bx_item_list_slide" style="height: auto;">
			<?foreach($arResult['ELEMENTS'] as $product):
			/**
			 * @var ProductInterface $product
			 */
			?>
			<div class="bx_catalog_item" style="position: relative">
				<div class="">
					<a href="<?=$product->getDetailPageUrl()?>"
					   class="bx_catalog_item_images"
					   style="background-image: url('<?=$product->getPreviewPicture()?>')"
					   title="<?=$product->getName()?>">
					</a>
					<div class="bx_catalog_item_title">
						<a href="<?=$product->getDetailPageUrl()?>" title="<?=$product->getName()?>">
							<?=$product->getName()?>
						</a>
					</div>
					<div class="bx_catalog_item_price">
						<div id="bx_1182278561_66_price" class="bx_price"><?=$product->getPrice(true)?></div>
					</div>
				</div>
				<div class="delete-recent-item"></div>
			</div>
			<?endforeach?>
			<div style="clear: both;"></div>
		</div>
	</div>
</div>
