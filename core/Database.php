<?php

$config = require_once __DIR__ . '/config.php';

try {
  $dsn="mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}";
  $pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  die("Database connection failed: " . $e->getMessage());
}
