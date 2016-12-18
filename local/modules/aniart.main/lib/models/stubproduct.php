<?php


namespace Aniart\Main\Models;


use Aniart\Main\Interfaces\ProductInterface;

class StubProduct implements ProductInterface
{

	public function getPreviewPicture()
	{
		return '/upload/iblock/0cb/0cbcdd686c12b9217dee4c3367cec4a9.jpg';
	}

	public function getName()
	{
		return 'Товар '.randString(8);
	}

	public function getPrice($format = false)
	{
		$price = rand(1, 1000);
		return $format ? CurrencyFormat($price, 'RUB') : $price;
	}

	public function getDetailPageUrl()
	{
		return '/catalog/pants/pants-flower-glade/';
	}
}