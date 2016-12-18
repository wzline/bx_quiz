<?php


namespace Aniart\Main\Repositories;


class SeoPagesRepository extends AbstractHLBlockElementsRepository
{
	public function newInstance(array $fields)
	{
		return app('SeoPage', array($fields));
	}
}