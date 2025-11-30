<?php
namespace App\Services;

use App\Email\EmailTemplateRenderer;
use App\Domain\Entity\Book;
use DateTime;

interface EmailServices {
  public function sendVerificationEmail(string $to, string $userName, string $code): bool;
  public function sendResetPasswordEmail(string $to, string $userName, string $token, int $expiresMinutes): bool;
  public function sendReservationBook(string $to, string $userName, Book $book, DateTime $expiresAt): bool;
  public function sendExtendReservation(string $to, string $userName, int $reservationId, string $isbn, DateTime $newDate): bool;
  public function sendCancelReservation(string $to, string $userName, int $reservationId): bool;
}