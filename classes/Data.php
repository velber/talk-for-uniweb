<?php

require_once 'MySQL.php';

class Data
{
    public $deleteData = array();

    public $newData    = array();

    public $updateData = array();

    private $inputData;

    private $actualData;

    private $_db;

    /**
     * Data constructor.
     * Підключення до БД.
     *
     * @param array $inputData масив вхідних даних з GET запроса.
     */
    public function __construct($inputData)
    {
        // connection to DB
        $this->_db = MySQL::getInstance()->getConnect();

        $this->inputData = $inputData;
    }


    /**
     * Гет всіх даних з БД.
     *
     * @return $this
     */
    public function findAll()
    {
        $stmt = $this->_db->query("SELECT * FROM data");
        $this->actualData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this;
    }

    /**
     * Сравнение двух массивов содержащих входных данных из GET
     * запроса и актуальных данных из базы данних.
     *
     * @return $this
     */
    public function processData()
    {
        $actualDataAssoc = array();

        foreach ($this->actualData as $actualData) {

            $actualDataAssoc[$actualData['ident']] = array(
                'value'   => $actualData['value'],
                'version' => $actualData['version'],
            );;

            // поиск значений и версий по идентификаторам,
            // которые отсутствуют в пришедшем запросе, но есть в БД
            if (!in_array($actualData['ident'], $this->inputData['ident'])) {
                $this->newData[$actualData['ident']] = array(
                    'value'   => $actualData['value'],
                    'version' => $actualData['version'],
                );
            }
        }

        // поиск идентификаторов, которые пришли в запросе и отсутствуют в БД
        $keysFromActualData = array_keys($actualDataAssoc);

        $this->deleteData = array_filter($this->inputData['ident'], function ($item) use ($keysFromActualData) {
            return !in_array($item, $keysFromActualData);
        });

        $this->deleteData = array_values($this->deleteData);

        // поиск данных где версия в БД стала больше чем версия пришедшая в запросе
        foreach ($this->inputData['ident'] as $key => $item) {
            if (key_exists($item, $actualDataAssoc)
                && $this->inputData['version'][$key] < $actualDataAssoc[$item]['version']) {
                $this->updateData[$item] = array(
                    'value'   => $actualDataAssoc[$item]['value'],
                    'version' => $actualDataAssoc[$item]['version'],
                );
            }
        }

        return $this;
    }

    /**
     * Повертає серіалізований масив в 3 ключами.
     *
     * @return array
     */
    public function getSerializedData()
    {
        return serialize(
            array(
                'delete' => $this->deleteData,
                'update' => $this->updateData,
                'new' => $this->newData,
            )
        );
    }
}
