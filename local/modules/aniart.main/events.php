<?php
/**
 * Регистрация обработчиков системный событий
 */
use Bitrix\Main\EventManager;
$eventManager = EventManager::getInstance();

$eventManager->addEventHandler('main', 'OnEpilog', ['\Aniart\Main\Observers\BitrixObserver', 'onEpilog']);
$eventManager->addEventHandler('main', 'OnProlog', ['\Aniart\Main\Observers\BitrixObserver', 'onProlog']);