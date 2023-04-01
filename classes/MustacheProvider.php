<?php
// chci jedinou instanci mustache a poskytovat ji na požádání
class MustacheProvider
{
    private static ?Mustache_Engine $engine = null;

    public static function get()
    {
        //když existuje instance, vrátí ji, jinak ji nejprve vytvoří
        if (!self::$engine)
            self::createInstance();

        return self::$engine;
    }

    private static function createInstance() : void
    {
        self::$engine = new Mustache_Engine([
            'entity_flags' => ENT_QUOTES,
            "loader" => new Mustache_Loader_FilesystemLoader(__DIR__ . "/../templates")
        ]);
    }
}