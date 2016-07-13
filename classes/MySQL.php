<?php

class MySQL
{
    /**
     * Екземпляр даного класу
     * @var object
     */
    private static $_instance;

    /**
     * Об’єкт PDO
     * @var \PDO
     */
    private $dbh;

    /**
     * Приватний метод. Створює об’єкт PDO
     * return $this
     */
    private function __construct()
    {
        //connection string example - mysql://username:password@dbhost/dbname
        $url = parse_url(getenv("DATABASE_URL"));

        $dbName = substr($url["path"], 1);
        $dbHost = $url["host"];

        try {
            $this->dbh = new PDO(
                "mysql:dbname={$dbName};host={$dbHost}",
                $url['user'],
                $url['pass']
            );
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Заборонаєм клонування
     */
    private function __clone() {}

    public static function getInstance()
    {
        // проверяем актуальность экземпляра
        if (null === self::$_instance) {

            // создаем новый экземпляр
            self::$_instance = new self();
        }

        // возвращаем созданный или существующий экземпляр
        return self::$_instance;
    }

    /**
     * Гет об’єкт PDO
     * @return \PDO
     */
    public function getConnect()
    {
        return $this->dbh;
    }
}
