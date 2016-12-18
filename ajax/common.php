<?php
define("NOT_CHECK_PERMISSIONS", true);
define('STOP_STATISTICS', true);
define('BX_SECURITY_SHOW_MESSAGE', true);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$handler = trim($_REQUEST['handler']);
if(!empty($handler) && ($ajaxHandler = \Aniart\Main\Ajax\AjaxHandlerFactory::build($handler))){
    $ajaxHandler->start();
}
else{
    echo "Не найден ajax-обработчик";
}
die;