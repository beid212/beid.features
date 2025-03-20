<? 

defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\IO\Path;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Loader;

final class beid_features extends CModule
{
    public static $sModuleName = 'beid.features';


    public function __construct()
    {
        //все getMessage брать из lang/ru/install/index.php
        $this->MODULE_ID = self::$sModuleName;//id модуля
        $this->MODULE_NAME = Loc::getMessage('MODULE_NAME');//название модуля
        $this->MODULE_DESCRIPTION = Loc::getMessage('MODULE_DESCRIPTION');//описание модуля
        $this->PARTNER_NAME = Loc::getMessage('MODULE_PARTNER_NAME');//партнёр модуля
        $this->PARTNER_URI = Loc::getMessage('MODULE_PARTNER_URI');//ссылка на партнёра

        //подключаем файл версии модуля
        /** @var array{MODULE_VERSION: string, MODULE_VERSION_DATE: string} $version */
        $version = include __DIR__ . '/version.php';

        $this->MODULE_VERSION = $version['MODULE_VERSION'];//ставим версию модуля
        $this->MODULE_VERSION_DATE = $version['MODULE_VERSION_DATE'];//ставим дату изменения/создания модуля
    }

    //метод устанавливает необоходимые параметры для работы модуля
    public function DoInstall(): void
    {
        //устанавливаем значения для базы данных/прочих дополнительных регистраций модуля
        $this->InstallDB();

        //устанавливаем файлы для модуля
        $this->InstallFiles();
    }

    public function InstallDB()
    {
        //регистрируем модуль
        ModuleManager::registerModule($this->MODULE_ID);
        //подключаем
        Loader::requireModule($this->MODULE_ID);
        
        $connection = \Bitrix\Main\Application::getConnection();

        foreach($this->getTables() as $tableClass){
            if(!$connection->isTableExists($tableClass::getTableName())) {
                $tableClass::getEntity()->createDbTable();
            }
        }
    }

    public function getTables(): array
    {
        return [
            Beid\Features\Tables\FeaturesTable::class
        ];
    }

    public function InstallFiles(): void
    {
        try {
            //копируем файлы компонентов
            CopyDirFiles(
                //
                Path::combine(__DIR__, '/files/components/'),
                Path::convertRelativeToAbsolute(Path::combine('bitrix/components/'.$this->MODULE_ID .'/')),
                true,
                true
            );
        } catch (ArgumentNullException|ArgumentTypeException) {
            // Noop, never happens.
        }

        //копируем файлы публичной части
        CopyDirFiles(
            Path::combine(__DIR__, '/files/public/'),
            Application::getDocumentRoot(),
            true,
            true
        );
    }

    //метод удаляет файлы модуля
    public function DoUninstall(): void
    {
        //удаляем все файлы модуля
        $this->UnInstallFiles();
        $this->UnInstallDB();
    }
    
    public function UnInstallDB(): void
    {
        Loader::requireModule($this->MODULE_ID);

        $connection = Application::getInstance()->getConnection();

        foreach($this->getTables() as $tableClass){
            if ($connection->isTableExists($tableClass::getTableName())) {
                $connection->dropTable($tableClass::getTableName());
            }
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
        Loader::clearModuleCache($this->MODULE_ID);
    }

    public function UnInstallFiles(): void
    {
        //удаление компонентов
        DeleteDirFilesEx('bitrix/components/'.$this->MODULE_ID .'');

        //удалить папки которые были добавлены в public
        $paths = $this->getTopLevelPathes(__DIR__.'/files/public/');
        //проходим массивом по наименованиям установленных модулем файлов и удаляем их
        foreach($paths as $path)
        {
            DeleteDirFilesEx($path);
        }
    }

    //метод берёт название верхних папок/файлов от public модуля и возвращает массив названий
    //нужно чтобы удалить все файлы/папки из public
    public function getTopLevelPathes(string $directoryPath): array
    {
        $paths = [];
        // Проверяем, существует ли каталог
        if (!is_dir($directoryPath)) {
            error_log("Ошибка: Каталог не существует: " . $directoryPath);
            return [];
        }

        // Открываем каталог
        if ($handle = opendir($directoryPath)) {
            // Читаем содержимое каталога
            while (false !== ($entry = readdir($handle))) {
                // Игнорируем текущую и родительскую директории
                if ($entry == "." || $entry == "..") {
                    continue;
                }
                $paths[] = $entry;
                // Вызываем callback-функцию
            }

            // Закрываем каталог
            closedir($handle);

            return $paths;
        } else {
            error_log("Ошибка: Не удалось открыть каталог: " . $directoryPath);
            return [];
        }
    }
}