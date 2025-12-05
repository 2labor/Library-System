<?php
/**
 * Purpose: Interface defining user profile and management HTTP endpoints.
 *
 * Responsibilities:
 * - Declare method signatures for user profile get/update
 * - Define contracts for user listing (admin/staff), deletion
 *
 * Inputs:
 * - Path parameter (userId) in relevant methods
 *
 * Outputs:
 * - Methods return void; implementations emit JSON with user DTOs
 
 *
 * File: app/controller/UserController.php
 */

namespace App\Controller;

interface UserController {
  public function createUser(): void;
  public function login(): void;
  public function logout(): void;
  public function getUserById(int $id): void;
  public function deleteUser(int $id): void;
  public function updateUser(): void;
}