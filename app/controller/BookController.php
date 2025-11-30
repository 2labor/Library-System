<?php
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