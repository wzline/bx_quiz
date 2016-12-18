<?php
/**
 *  Узконаправленные  функции, которые работают в рамках конкретного проекта
 */
/**
 * @param null $key
 * @param null $value
 * @param bool|false $const
 * @return mixed|\Aniart\Main\Interfaces\RegistryInterface
 */
function registry($key = null, $value = null, $const = false) {
	$registry = app() -> getRegistry();
	if (is_null($key)) {
		return $registry;
	} elseif (is_string($key) && is_null($value)) {
		return $registry -> get($key);
	} else {
		$registry -> set($key, $value, $const);
	}
}

/**
 * Если задан $abstract, то выполняется метод \Aniart\Main\App::make($abstract, $parans), в противном случае
 * возвращается экземпляр класса \Aniart\Main\App
 * @param string|null $abstract абстрактное представление сущности
 * @param array $params дополнительные параметры для создания сущности
 * @return \Aniart\Main\App|mixed
 */
function app($abstract = null, $params = array()) {
	$app = Aniart\Main\App::getInstance();
	if (is_null($abstract)) {
		return $app;
	}
	return $app->make($abstract, $params);
}

function seo($paramName = null, $paramValue = null, $overwrite = false)
{
    /**
     * @var \Aniart\Main\Seo\SeoParamsCollector $seo;
     */
    $seo = app('SeoParamsCollector');
    if(!is_null($paramName)){
        if(is_null($paramValue)){
            return $seo->getParamValue($paramName);
        }
        else{
            return $seo->setParamsValue($paramName, $paramValue, $overwrite);
        }
    }
    return $seo;
}
