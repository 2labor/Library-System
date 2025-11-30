<?php
namespace App\Controller;

interface UserController {

  public function createUser(): void;
  public function login(): void;
  public function logout(): void;
  public function getUserById(int $id): void;
  public function deleteUser(int $id): void;
  public function updateUser(): void;
}