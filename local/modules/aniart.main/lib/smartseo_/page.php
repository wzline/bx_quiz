<?php 

namespace Aniart\Main\SmartSeo;

use Aniart\Main\Models\AbstractModel;



class Page extends AbstractModel
{
	protected $parser;
	protected $interpreter;
	
	public function __construct(array $fields = array())
	{
		if(!isset($fields['vars'])){
			$fields['vars'] = array();
		}
		parent::__construct($fields);
	}
	
	public function setVars(array $vars)
	{
		$this->vars = $vars;
		return $this;
	}
	
	public function getVars()
	{
		return $this->vars;
	}
	
	public function addVar($name, $value)
	{
		$this->fields['vars'][$name] = $value;
		return $this;
	}
	
	public function getUriVars()
	{
		return (array)$this->uri_vars;
	}
	
	public function getParser()
	{
		if(is_null($this->parser)){
			$this->setParser(new Parser());
		}
		return $this->parser;
	}	
	
	public function setParser(Parser $parser)
	{
		$this->parser = $parser;
	}
	
	public function getInterpreter()
	{
		if(is_null($this->interpreter)){
			$this->setInterpreter(new Interpreter());
		}
		return $this->interpreter;
	}	
	
	public function setInterpreter(Interpreter $interpreter)
	{
		$this->interpreter = $interpreter;
		$interpreter->setPage($this);
	}
	
	public function getCode()
	{
		return $this->code;
	}

    public function getPriority()
    {
        return (int)$this->priority;
    }
	
	/**
	 * 
	 * @param string|array|null $path
	 */
	public function compile($paths = null)
	{
		if(is_null($paths)){
			$paths = $this->compile;
		}
		if(is_string($paths) && !empty($paths)){
			$paths = array($paths);
		}
		if(is_array($paths) && !empty($paths)){
			foreach($paths as $path){
				$values = $this->getPathValues($path);
				if(!$values){
					continue;
				}
				if($compiled = $this->doCompile($values)){
					$this->setPathValues($path, $compiled);					
				}
			}
		}
		return $this;
	}
	
	/**
	 * 
	 * @param array|string $value
	 */
	protected function doCompile($fieldValue, $fieldName = '')
	{
		$result = false;
		if(is_array($fieldValue)){
			foreach($fieldValue as $k => $value){
				$result[$k] = $this->doCompile($value, $k);
			}
		}
		elseif(is_string($fieldValue) && !empty($fieldValue)){
			$parser = $this->getParser();
			$parser->setData($fieldValue);
			$fieldExpression = $parser->parse();
			$interpreter = $this->getInterpreter();
			$result = $interpreter->interpret($fieldExpression);
			$this->addVar($fieldName, $result);
		}
		return $result;
	}
	
	private function getPathValues($path)
	{
		$result = false;
		$values = $this->fields;
		$path   = explode('.', $path);
		foreach($path as $p){
			if(isset($values[$p])){
				$values = $values[$p]; 
				$result = true;
			}
		}
		return $result ? $values : false;
	}
	
	private function setPathValues($path, $values)
	{
		$fields = &$this->fields;
		$path = explode('.', $path);
		foreach($path as $p){
			if(isset($fields[$p])){
				$fields = &$fields[$p];
			}
		}
		$fields = $values;
		unset($fields);
	}
}