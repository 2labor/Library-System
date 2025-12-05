<?php

/**
 * Purpose: Database connection factory; creates and manages PDO instance.
 *
 * Responsibilities:
 * - Initialize PDO connection using config credentials
 * - Set connection attributes (error mode, fetch mode)
 * - Provide singleton or fresh PDO instance to router/services
 *
 * Inputs:
 * - Configuration array with db_host, db_name, db_user, db_pass
 *
 * Outputs:
 * - PDO instance ready for prepared statements
 *
 * Errors:
 * - Catches PDO exception on connection failure
 * - Prints error message and exits on connection error
 *
 * File: core/Database.php
 */
$config = require_once __DIR__ . '/config.php';

try {
  $dsn="mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}";
  $pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  die("Database connection failed: " . $e->getMessage());
}
