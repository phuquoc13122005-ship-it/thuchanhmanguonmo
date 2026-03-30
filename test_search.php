<?php
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/models/ProductModel.php';

$db = (new Database())->getConnection();
$model = new ProductModel($db);
$results = $model->searchProducts('hạt kitekat');
header('Content-Type: text/plain');
print_r($results);
?>
