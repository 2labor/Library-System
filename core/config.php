<?php
/**
 * Purpose: Application configuration constants and settings.
 *
 * Responsibilities:
 * - Export database credentials (host, name, user, password)
 * - Define email service credentials (SMTP server, sender)
 *
 * Inputs:
 * - Environment variables or hardcoded values
 *
 * Outputs:
 * - Associative array of configuration values
 *
 * Errors:
 * - Should validate that critical keys are present
 * - Returns empty/default values if keys missing (or throws exception)
 *
 * File: core/config.php
 */

return [
  'db_host' => '127.0.0.1',          
  'db_name' => 'library_app',         
  'db_user' => 'app_user',           
  'db_pass' => '12345',    
  'db_charset' => 'utf8mb4', 
  
  'email' => [
    'default_from' => 'no-reply@gmail.com',
    'service' => 'gmail',
    'gmail' => [
      'host' => 'smtp.gmail.com',
      'port' => 587,
      'username' => 'beehappyyyyyyy@gmail.com',
      'password' => 'wpsoesplitljilyg',
    ],
  ],
];