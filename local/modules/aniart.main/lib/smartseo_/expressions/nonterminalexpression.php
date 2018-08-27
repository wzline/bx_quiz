<?php

namespace Aniart\Main\SmartSeo\Expressions;

use Aniart\Main\SmartSeo;

class NonTerminalExpression extends AbstractExpression
{
    /**
     * @var AbstractExpression
     */
    protected $children;

    public function __construct($name, $children = array())
    {
    	parent::__construct($name);
        $this->setChildren($children);
    }

    /**
     * @return AbstractExpression[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param AbstractExpression[] $children
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }

    public function addChild(AbstractExpression $child)
    {
        $this->children[] = $child;
        return $this;
    }

    /**
     * Ищет по имени первый дочерный элемент
     * @param $name
     * @return AbstractExpression|false
     */
    public function getFirstByName($name)
    {
        foreach($this->getChildren() as $child){
            if($child->getName() == $name){
                return $child;
            }
            elseif($child instanceof NonTerminalExpression){
                return $child->getFirstByName($name);
            }
        }
        return false;
    }
}