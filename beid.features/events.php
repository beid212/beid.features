<?
use Bitrix\Main\EventManager;
use Beid\Features\Events\Main;

$eventManager = EventManager::getInstance();

$eventManager->addEventHandler('main', 'OnEpilog', [Main\Customize::class, 'addFeaturesManagement']);
$eventManager->addEventHandler('main', 'OnBeforeEndBufferContent', [Main\Customize::class, 'setFeatures']);