<?php

require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'trest_init.php');
require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Sandwich.php');
require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Ingredient.php');

header('Content-Type: application/json');

$id = @$_GET['id'];
$id = (int)$id;


if($id){
    $result = Ingredient::find($id);
} else {
    $result = Ingredient::findAll();
}

echo json_encode($result);