<?php

//header("Content-Type: text/plain");

require_once 'classes/Data.php';

$argc = array(
    'ident' => array(
        'filter' => FILTER_SANITIZE_STRING,
        'flags'  => FILTER_REQUIRE_ARRAY,
    ),
    'value' => array(
        'filter' => FILTER_SANITIZE_STRING,
        'flags'  => FILTER_REQUIRE_ARRAY,
    ),
    'version' => array(
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_REQUIRE_ARRAY,
    )
);

//філтр даних з GET запроса
$inputData = filter_input_array(INPUT_GET, $argc);

$data = new Data($inputData);

//гет всіх даних з БД і порівняня з вхідними даними
$data
    ->findAll()
    ->processData();

echo $data->getSerializedData();
