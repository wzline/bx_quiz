<?php


namespace Aniart\Main\SmartSeo\Repositories;


use Aniart\Main\Models\SeoPage;

class HLBlockPagesRepository extends AbstractPagesRepository
{
	/**
	 * @var SeoPage[]
	 */
	protected static $pages;

	protected static function getPages(){
		if(is_null(static::$pages)){
			static::obtainPages();
		}
		return static::$pages;
	}

	protected static function obtainPages()
	{
		static::$pages = app('SeoPagesRepository')->getList(array('UF_SORT' => 'ASC'));
	}

	public function getByUri($uri)
	{
		$uri = $this->normalizeUri($uri);
		foreach(static::getPages() as $page){
			$uriPattern = $page->getPageUri();
			$pageData = $this->extractPageData($page);
			if($page = $this->completePage($uri, $uriPattern, $pageData)){
				return $page;
			}
		}
		return false;
	}

	public function extractPageData(SeoPage $page)
	{
		$pageData = array(
			'uri_vars' => [],
			'compile' => ['seo'],
			'seo' => [
				'page_title' => $page->getPageTitle(),
				'meta_title' => $page->getMetaTitle(),
				'keywords' => $page->getKeywords(),
				'description' => $page->getDescription()
			]
		);

		return $pageData;
	}
}