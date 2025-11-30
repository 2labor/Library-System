<?php
namespace App\Controller;

interface BookReservationController {
  public function reserve(): void;
  public function extend(): void;
  public function cancel(): void;
  public function getByBook(string $isbn): void;
  public function getByUser(int $userId): void;
}
