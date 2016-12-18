<?php
namespace Aniart\Main\Seo;

use Aniart\Main\Interfaces\SeoParamsInterface;
use Bitrix\Main\Type\ParameterDictionary;

class SeoParamsCollector extends ParameterDictionary
{
    public function __construct()
    {
        $this->setValuesNoDemand(array(
            'page_title' => null,
            'meta_title' => null,
            'keywords' => null,
            'description' => null,
            'robots' => 'index, follow',
            'canonical' => null,
            'chain_items' => array(),
            'pagination_params' => array()
        ));
        parent::__construct(null);
    }

    public function fetchSeoParams(SeoParamsInterface $entity)
    {
        $this->setPageTitle($entity->getPageTitle());
        $this->setMetaTitle($entity->getMetaTitle());
        $this->setKeywords($entity->getKeywords());
        $this->setDescription($entity->getDescription());
    }

    public function process()
    {
        global $APPLICATION;
        foreach($this as $key => $value) {
            if ($key == 'chain_items') {
                foreach ($value as $item) {
                    $APPLICATION->AddChainItem($item['TITLE'], $item['LINK']);
                }
            } elseif ($key == 'pagination_params') {
                if ($prevPageLink = $value['links']['prev_page']) {
                    $APPLICATION->SetPageProperty('pagination_prev', '<link rel="prev" href="' . $prevPageLink . '" />');
                }
                if ($nextPageLink = $value['links']['next_page']) {
                    $APPLICATION->SetPageProperty('pagination_next', '<link rel="next" href="' . $nextPageLink . '" />');
                }
            } else {
                if (is_null($value)) {
                    continue;
                } elseif ($key == 'meta_title') {
                    $APPLICATION->SetTitle($value);
                } else {
                    $APPLICATION->SetPageProperty($key, $value);
                }
            }
        }
    }

    protected static function format($str)
    {
        $str = trim($str);
        if(!empty($str)){
            $str = preg_replace('/\s{2,}/', ' ', $str);
            $str = str_replace(array(' . ', ' , '), array('. ', ', '), $str);
        }
        return $str;
    }

    public function setPaginationParams($params, $overwrite = false)
    {
        return $this->setParamsValue('pagination_params', $params, $overwrite);
    }

    public function setRobots($robots, $overwrite = false)
    {
        return $this->setParamsValue('robots', $robots, $overwrite);
    }

    public function setPageTitle($pageTitle, $overwrite = false){
        return $this->setParamsValue('page_title', $pageTitle, $overwrite);
    }

    public function setMetaTitle($metaTitle, $overwrite = false){
        return $this->setParamsValue('meta_title', $metaTitle, $overwrite);
    }

    public function setKeywords($keywords, $overwrite = false){
        return $this->setParamsValue('keywords', $keywords, $overwrite);
    }

    public function setDescription($description, $overwrite = false){
        return $this->setParamsValue('description', $description, $overwrite);
    }

    public function setChainItems(array $chainItems = array(), $overwrite = false)
    {
        return $this->setParamsValue('chain_items', $chainItems, $overwrite);
    }

    public function setCanonical($link, $overwrite = false)
    {
        return $this->setParamsValue('canonical', $link, $overwrite);
    }

    public function addChainItem($title, $link = false)
    {
        global $APPLICATION;
        if($link === false){
            $link = $APPLICATION->GetCurDir();
        }
        $this->values['chain_items'][] = array('TITLE' => $title, 'LINK' => $link);
    }

    public function addChainItemFromPageTitle($link = false)
    {
        $pageTitle = $this->getPageTitle();
        if($pageTitle){
            $this->addChainItem($pageTitle, $link);
        }
    }

    public function getChainItems(){
        return $this->getParamValue('chain_items');
    }

    public function getRobots(){
        return $this->getParamValue('robots');
    }

    public function noIndex()
    {
        $this->setRobots('noindex, nofollow');
        return $this;
    }

    public function getCanonical(){
        return $this->getParamValue('canonical');
    }

    public function getPaginationParams(){
        return $this->getParamValue('pagination_params');
    }

    public function getPageTitle(){
        return $this->format($this->getParamValue('page_title'));
    }

    public function getMetaTitle(){
        return $this->format($this->getParamValue('meta_title'));
    }

    public function getKeywords(){
        return $this->format($this->getParamValue('keywords'));
    }

    public function getDescription(){
        return $this->format($this->getParamValue('description'));
    }

    public function setParamsValue($paramName, $paramValue, $overwrite = false)
    {
        if(
            $this->offsetExists($paramName) &&
            (($paramValue !== $this->getRaw($paramName)) ||  $overwrite)
        ){
            $this->values[$paramName] = $paramValue;
        }
        return $this;
    }

    public function getParamValue($paramName)
    {
        return $this->offsetGet($paramName);
    }
}
?>