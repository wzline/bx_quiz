<?php

namespace Aniart\Main\SmartSeo\Expressions;

use Aniart\Main\SmartSeo;

abstract class AbstractExpression implements SmartSeo\Interfaces\InterpretedInterface
{
	protected $pattern;
    protected $name;
    
    public function __construct($pattern = null)
    {
    	if(!is_null($pattern)){
    		$this->setPattern($pattern);
    	}
    }
    
    public function setPattern($pattern)
    {
    	$this->pattern = $pattern;
    	return $this;
    }
    
    public function getPattern()
    {
    	return $this->pattern;
    }

    public function setName($name)
    {
        $this->name = $name;
   		return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function interpret(SmartSeo\Interfaces\InterpreterInterface $interpreter)
    {
    	return $interpreter->interpret($this);
    }
}