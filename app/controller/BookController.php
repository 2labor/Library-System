<?php
/**
 * Purpose: Interface defining book management HTTP endpoints.
 *
 * Responsibilities:
 * - Declare method signatures for book CRUD (add, update, delete, find)
 * - Define contracts for availability toggling and specific book queries
 *
 * Inputs:
 * - Path parameters (isbn) in relevant methods
 *
 * Outputs:
 * - Methods return void; implementations emit JSON with book DTOs
 *
 * File: app/controller/BookController.php
 */
namespace App\Controller;

interface BookController {
  public function add(): void;
  public function update(string $isbn): void;
  public function delete(string $isbn): void;
  public function find(): void;
  public function findAllAvailable(): void;
  public function toggleAvailability(string $isbn): void;
  public function findByIsbn(string $isbn): void;
}