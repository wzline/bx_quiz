<?php 

namespace Aniart\Main\SmartSeo;

use Aniart\Main\SmartSeo\Interfaces\PagesRepositoryInterface;
use Aniart\Main\Traits\Singleton;

class SmartSeo
{
	use Singleton;

	protected $page;
	protected $compiledPage;
    /**
     * @var PagesRepositoryInterface
     */
    protected static $repository = null;
	
	public function init(Interfaces\PagesRepositoryInterface $repository)
	{
		global $APPLICATION;
		$this->page = $repository->getByUri($APPLICATION->GetCurPage());
		if(!$this->page){
			$this->page = $repository->newInstance(); //болванчик
		}
        $this->repository = $repository;
    }
	
	public function isPageFound()
	{
		return !!$this->page->uri;
	}

    public function obtainPage($pageUri)
    {
        $this->page = $this->repository->getByUri($pageUri);
    }
	
	public function getPage()
	{
		return $this->page;
	}
	
	public function obtainPageMeta(array $vars = array(), $lang = null)
	{
		if(self::isPageFound()){
			$seoKey = 'seo';
			$this->compiledPage = clone $this->page;
			if($lang){
				$seoKey.= '.'.$lang;
			}
			$this->compiledPage->setVars($vars);
			$this->compiledPage->compile($seoKey);
		}
		return;
	}
	
	public function getPageMeta(array $vars = array(), $lang = null)
	{
		$result = array();
		if(is_null($this->compiledPage)){
			self::obtainPageMeta($vars, $lang);
		}
		if($this->compiledPage){
			$result = $lang ? $this->compiledPage->seo[$lang] : $this->compiledPage->seo;
			$result = (array)$result;
		}
		return $result;
	}
}