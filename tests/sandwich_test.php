<?php

require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TRest' . DIRECTORY_SEPARATOR . 'trest_init.php');
require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Sandwich.php');
require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Ingredient.php');

header('Content-Type: application/json');

$id = @$_GET['id'];
$id = (int)$id;
 

if($id){
    $result = Sandwich::findOne($id);
    $result->ingredients[0]->name = 'jamon';
    $result->title = "el mio";
    $result->price = 9200;
    ($result->save());
    $result = Sandwich::find()->setCacheTtl(000)->findOne($id);
} else {
    $result = Sandwich::find()->all();
}

echo json_encode($result);