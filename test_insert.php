<?php
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/models/CategoryModel.php';

$db = (new Database())->getConnection();
$model = new CategoryModel($db);

$res = $model->addCategory('Test API Add', 'Test description');
var_dump($res);

$cats = $model->getCategories();
var_dump($cats);
