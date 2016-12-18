<?php 
namespace Aniart\Main\Models;

use Aniart\Main\Interfaces\SeoParamsInterface;
use Aniart\Main\Interfaces\VisitableInterface;
use Aniart\Main\Interfaces\VisitorInterface;
use Aniart\Main\Models\IblockSectionModel as Section;
use Bitrix\Iblock\InheritedProperty\ElementValues;

class IblockElementModel extends AbstractModel implements VisitableInterface, SeoParamsInterface
{
	protected $sections;
    protected $seoParams;

    public function getId()
    {
        return (int)$this->ID;
    }

    public function getIblockId()
    {
        return $this->IBLOCK_ID;
    }

    public function getName()
    {
        return $this->NAME;
    }

	public function getCode()
	{
		return $this->CODE;
	}

	public function isActive()
	{
		return $this->ACTIVE == 'Y';
	}
	
	public function getCreatedTimestamp()
	{
		return \MakeTimeStamp($this->DATE_CREATE);
	}

	public function obtainSections()
	{
		$rsSections = \CIBlockElement::GetElementGroups($this->getId(), true);
		while($arSection = $rsSections->GetNext()){
			$this->sections[$arSection['ID']] = new Section($arSection);
		}
		return $this;
	}
	/**
	 * Элемент может принадлежать несколим секциям
	 */
	public function getSections()
	{
		if(is_null($this->sections)){
			$this->obtainSections();
		}
		return $this->sections;
	}

	public function getSectionsId()
	{
		return array_keys($this->getSections());
	}

	public function getSectionId()
	{
		return $this->IBLOCK_SECTION_ID;
	}
	
	public function getSection()
	{
        if(!isset($this->SECTION) && $this->getSectionId() > 0){
            $this->SECTION = \CIBlockSection::GetByID($this->getSectionId())->Fetch();
        }
		if(is_array($this->SECTION)){
			$this->SECTION = new Section($this->SECTION);
		}
		return $this->SECTION;
	}

    public function accept(VisitorInterface $visitor)
    {
        return $visitor->visit($this);
    }

    /**
     * @param $propName
     * @param bool|false $index
     * @return mixed
     */
    public function getPropertyValue($propName, $index = false)
    {
        $result = false;
        if(!empty($propName)){
            $propValue = $this->{'PROPERTY_'.$propName.'_VALUE'};
            $propValue = !is_null($propValue) ? $propValue : $this->PROPERTIES[$propName]['VALUE'];
            $propValue = !is_null($propValue) ? $propValue : $this->{'PROPERTY_'.$propName};
            if($propValue && $index !== false){
                $propValue = $propValue[$index];
            }
            $result = $propValue;
        }
        return $result;
    }

    public function getPropertyDescription($propName, $index = false)
    {
        $result = false;
        if(!empty($propName)){
            $propValue = $this->{'PROPERTY_'.$propName.'_DESCRIPTION'};
            $propValue = $propValue ? $propValue : $this->PROPERTIES[$propName]['DESCRIPTION'];
            $propValue = $propValue ? $propValue : $this->{'DESCRIPTION_'.$propName};
            if($propValue && $index !== false){
                $propValue = $propValue[$index];
            }
            $result = $propValue;
        }
        return $result;
    }

    public function getPageTitle(){
        return $this->getSeoParamValue('ELEMENT_PAGE_TITLE');
    }

    public function getMetaTitle(){
        return $this->getSeoParamValue('ELEMENT_META_TITLE');
    }

    public function getKeywords(){
        return $this->getSeoParamValue('ELEMENT_META_KEYWORDS');
    }

    public function getDescription(){
        return $this->getSeoParamValue('ELEMENT_META_DESCRIPTION');
    }

    protected function getSeoParamValue($paramName)
    {
        $this->getSeoParams();
        return $this->seoParams[$paramName];
    }

    public function getSeoParams()
    {
        if(is_null($this->seoParams)){
            $this->obtainSeoParams();
        }
        return $this->seoParams;
    }

    public function obtainSeoParams()
    {
        $seoParamsValues = array();
        if(($iblockId = $this->getIblockId()) && ($id = $this->getId())) {
            $seoParams = new ElementValues($iblockId, $id);
            if ($seoParams) {
                $seoParamsValues = $seoParams->getValues();
            }
        }
        $this->seoParams = $seoParamsValues;

        return $this;
    }
}
?>