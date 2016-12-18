<?php
/**
 * Выводит на странице var_dump обрамленный в тег <pre>
 *  - если последний передаваемый параметр === true, то вызывается die;
 */
function pre_dump()
{
    $arguments	= func_get_args();
    $die		= array_pop($arguments);
    if(!is_bool($die)){
        $arguments[] = $die;
    }
    echo "<br clear='all' />";
    echo "<pre>";
    call_user_func_array('var_dump', $arguments);
    echo "</pre>";
    if($die === true){
        die;
    }
}

/**
 *  Выводит на странице var_dump обрамленный в тег <pre>, удаляет весь предшествующий вывод (для битрикса)
 *   - если последним параметром не указано false, то вызывается die;
 */

function pre_dump_clr()
{
    static $notToDiscard;
    global $APPLICATION;
    if(is_object($APPLICATION) && !$notToDiscard){
        $APPLICATION->RestartBuffer();
        $notToDiscard = true;
    }
    $arguments	= func_get_args();
    $arg_count	= count($arguments);
    if(!is_bool($arguments[$arg_count-1])){
        $arguments[] = true;
    }
    call_user_func_array('pre_dump', $arguments);
}

/*
 * Функция склонения числительных в рус. языке
 *
 * @param int    $number Число которое нужно просклонять
 * @param array  $titles Массив слов для склонения
 * @return string
 */
function DeclOfNum($number, $titles)
{
    $cases = array (2, 0, 1, 1, 1, 2);
    return $number." ".$titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
}

/**
 * ucfirst for multibyte encoding
 * @param string $string
 * @param string $encoding
 * @return string
 */
function mb_ucfirst($string, $encoding = 'utf-8')
{
    $strlen = mb_strlen($string, $encoding);
    $firstChar = mb_substr($string, 0, 1, $encoding);
    $then = mb_substr($string, 1, $strlen - 1, $encoding);
    return mb_strtoupper($firstChar, $encoding) . $then;
}
