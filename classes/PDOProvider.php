<?php

class PDOProvider
{
    private static ?PDO $pdo = null;

    public static function get()
    {
        //když existuje instance, vrátí ji, jinak ji nejprve vytvoří
        if (!self::$pdo)
            self::createInstance();

        return self::$pdo;
    }

    private static function createInstance() : void
    {
        $host = AppConfig::get("db.host");
        $db = AppConfig::get('db.db');
        $user = AppConfig::get("db.user");
        $pass = AppConfig::get("db.pass");
        $charset = AppConfig::get("db.charset");

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        self::$pdo = new PDO($dsn, $user, $pass, $options);
    }
}