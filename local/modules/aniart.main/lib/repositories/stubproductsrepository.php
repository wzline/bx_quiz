<?php


namespace Aniart\Main\Repositories;


class StubProductsRepository extends AbstractIblockElementRepository
{
	public function newInstance(array $fields = array())
	{
		return app('Product', [$fields]);
	}

	public function getItemsByIds(array $ids = [])
	{
		return [
			$this->newInstance(),
			$this->newInstance(),
			$this->newInstance(),
		];
	}
}