<?php
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
      'username' => '', // Add your email address
      'password' => '', // create your valid google-password
    ],
  ],
];