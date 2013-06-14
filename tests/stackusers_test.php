<?php

require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'trest_init.php');
require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'StackOverflowUser.php');
require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'StackOverflowUserBadge.php');

header('Content-Type: application/json');

$id = @$_GET['id'];
$id = (int)$id;
 

if($id){
    $result = StackOverflowUser::find($id);
} else {
    $result = StackOverflowUser::findAll();
}

echo json_encode($result);