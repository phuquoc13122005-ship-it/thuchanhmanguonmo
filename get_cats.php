<?php
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/models/CategoryModel.php';
$db = (new Database())->getConnection();
$model = new CategoryModel($db);
var_dump($model->getCategories());
