<?php 

namespace Aniart\Main\SmartSeo\Repositories;

use Aniart\Main\SmartSeo\Interfaces;
use Aniart\Main\SmartSeo\Page;

abstract class AbstractPagesRepository implements Interfaces\PagesRepositoryInterface
{

	public function newInstance(array $fields = array())
	{
		return new Page($fields);
	}

	public function getByCurrentUri()
	{
		global $APPLICATION;
		$uri = $APPLICATION->GetCurPage();
		return $this->getByUri($uri);
	}

	protected function completePage($uri, $uriPattern, $pageData)
	{
		$matches = array();
		if(substr($uriPattern, 0, 1) == '#'){
			$uriRegexp = $uriPattern;
		}else{
			$uriRegexp = '#^'.preg_quote($uriPattern, '#').'$#';
		}
		if(preg_match($uriRegexp, $uri, $matches)) {
			$pageData['uri'] = $uri;
			$pageData['uri_regexp'] = $uriRegexp;
			$pageData['uri_pattern'] = $uriPattern;
			if (isset($pageData['uri_vars']) && is_array($pageData['uri_vars'])) {
				$uriVars = array();
				array_walk($pageData['uri_vars'], function ($uriVar, $i) use (&$uriVars, $matches) {
					$uriVars[$uriVar] = isset($matches[$i + 1]) ? $matches[$i + 1] : '';
				});
				$pageData['uri_vars'] = $uriVars;
			}
			return $this->newInstance($pageData);
		}
		return false;
	}

	protected function normalizeUri($uri)
	{
		return str_replace(array('http://', 'https://', $_SERVER['SERVER_NAME']), '', $uri);
	}
}