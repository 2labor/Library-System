<?php
/*
 * Inputs:
 * - Methods for book reservation operations
 *
 * Outputs:
 * - BookReserved entity or boolean status for operations
 *
 * File: app/services/impl/BookReservationServicesImpl.php
 */
namespace App\Services\Impl;

use App\Repository\BookReservedRepository;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use App\Services\BookReservedServices;
use App\Services\EmailServices;
use App\Domain\Entity\BookReserved;
use Exception;
use DateTime;

class BookReservationServicesImpl implements BookReservedServices {

  private BookReservedRepository $repo;
  private UserRepository $userRepo;
  private BookRepository $bookRepo;
  private EmailServices $emailService;

  public function __construct(
    BookReservedRepository $repo,
    UserRepository $userRepo,
    BookRepository $bookRepo,
    EmailServices $emailService
  ) {
    $this->repo = $repo;
    $this->userRepo = $userRepo;
    $this->bookRepo = $bookRepo;
    $this->bookRepo = $bookRepo;
    $this->emailService = $emailService;
  }

  /**
   * Reserves a book for a user.
   *
   * @param string $isbn The ISBN of the book to reserve.
   * @param int $userId The ID of the user making the reservation.
   * @return BookReserved The created BookReserved entity.
   * @throws Exception If the user is not verified, book not found, book already reserved, or max reservations reached.
   */
 public function reserveBook(string $isbn, int $userId): BookReserved {
    $user = $this->userRepo->findUserById($userId)
        ?? throw new Exception("User not found!");

    if (!$user->getAccount()->isVerified()) {
        throw new Exception("Please verify your email before reserving book!");
    }

    $book = $this->bookRepo->findBookByIsbn($isbn)
        ?? throw new Exception("Book not found!");

    if ($this->repo->findActiveReservationByBookIsbn($isbn)) {
        throw new Exception("Book is already reserved!");
    }

    if (count($this->repo->findReservationsByUserId($userId)) >= 5) {
      throw new Exception("Max number of book reservation reached");
    }

    $reservedUntil = new DateTime('+3 months');

    $reservation = new BookReserved($isbn, $userId, $reservedUntil);

    $this->emailService->sendReservationBook(
        $user->getAccount()->getEmail(),
        $user->getName(),
        $book,
        $reservedUntil
    );

    $this->bookRepo->toggleAvailability($isbn);

    return $this->repo->create($reservation);
  }

  /**
   * Extends an existing book reservation.
   *
   * @param BookReserved $reservation The BookReserved entity to extend.
   * @return BookReserved The updated BookReserved entity.
   * @throws Exception If the reservation has already been extended once.
   */
  public function extendReservation(BookReserved $reservation): BookReserved {
    if($reservation->getCreatedAt() != $reservation->getUpdatedAt()) {
      throw new Exception("Reservation already extended once.");
    }

    $user = $this->userRepo->findUserById($reservation->getUserId());

    $reservedDate = $reservation->getReservedDate();
    $newDate = (clone $reservedDate)->modify('+3 months');
    $reservation->setReservedDate($newDate);

    $this->emailService->sendExtendReservation(
      $user->getAccount()->getEmail(),
      $user->getName(),
      $reservation->getId(),
      $reservation->getIsbn(),
      $newDate
    );

    return $this->repo->update($reservation);
  }

  /**
   * Cancels an existing book reservation.
   *
   * @param int $reservationId The ID of the reservation to cancel.
   * @return bool True if the reservation was successfully canceled.
   * @throws Exception If the reservation or user is not found, or if cancellation fails.
   */
  public function cancelReservation(int $reservationId): bool {
    $reservation = $this->repo->findReservationById($reservationId);
    if (!$reservation) {
        throw new Exception("Reservation not found.");
    }
    
    $user = $this->userRepo->findUserById($reservation->getUserId());
    if (!$user) {
        throw new Exception("User not found.");
    }

    $canceling = $this->repo->delete($reservationId);
    if (!$canceling) {
      throw new Exception("Failed to cancel reservation.");
    }

    $this->emailService->sendCancelReservation(
       $user->getAccount()->getEmail(),
       $user->getName(),
       $reservationId
    );

    $this->bookRepo->toggleAvailability($reservation->getIsbn());

    return $canceling;
  }

  /**
   * Retrieves a book reservation by book ISBN.
   *
   * @param string $isbn The ISBN of the book.
   * @return BookReserved|null The BookReserved entity if found, null otherwise.
   */
  public function getReservationByBook(string $isbn): ?BookReserved {
    return $this->repo->findReservationByBookIsbn($isbn);
  }

  /**
   * Retrieves all book reservations for a specific user.
   *
   * @param int $userId The ID of the user.
   * @return array An array of BookReserved entities.
   */
  public function getReservationByUserId(int $userId): array {
    return $this->repo->findReservationsByUserId($userId);
  }

}