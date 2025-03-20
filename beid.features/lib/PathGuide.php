<?
namespace Beid\Features;

class PathGuide
{
    private static ?self $pathGuide = null;
    private static $path;

    public static function up()
    {
        if(is_null(self::$pathGuide))
            self::$pathGuide = new static();
        
        return self::$pathGuide;
    }

    public function set(string $path)
    {
        if(!is_null($this->path))
            throw new \Exception('The main path for the folder has already been set!');
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);

        $pos = strpos($path, '/local/')===false? strpos($path, '/bitrix/'):strpos($path, '/local/');

        if ($pos === false)
            throw new \Exception('The module is not located in the local or bitrix folder!');
        $this->path = substr($path, $pos);
    }

    public function buildPath(string $additionalPath)
    {
        if(substr($additionalPath, 0, 1) !== '/')
            $additionalPath = '/'.$additionalPath;
        return $this->path.$additionalPath;
    }
}