<?php 
namespace Aniart\Main\SmartSeo\Repositories;

use Aniart\Main\SmartSeo\Page;

class JsonPagesRepository extends AbstractPagesRepository
{
	protected $pages;
	protected $file;
	
	public function __construct($fileAbsPath = null)
	{
		if(!is_null($fileAbsPath)){
			$this->setDataFile($fileAbsPath);
		}
	}

	public function setData(\stdClass $data)
	{
		$this->pages = $data;
	}
	
	public function setDataFile($absPath)
	{
		if(!file_exists($absPath)){
			throw new \RuntimeException('File "'.$absPath.'" not found');
		}
		$this->file = $absPath;
	}

	public function getByUri($uri)
	{
		$uri = $this->normalizeUri($uri);
		if(is_null($this->pages)){
			if(is_null($this->file)){
				throw new \RuntimeException('There is no data to find page');
			}
			$this->pages = json_decode(file_get_contents($this->file), true, 10);
			if(!is_array($this->pages)){
				throw new \UnexpectedValueException('Invalid JSON data');
			}
		}
		foreach($this->pages as $uriPattern => $pageData){
			if($page = $this->completePage($uri, $uriPattern, $pageData)){
				return $page;
			}
		}
		
		return false;
	}
}