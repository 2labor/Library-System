<?php
/*
 * Inputs:
 * - Methods for sending various types of emails
 *
 * Outputs:
 * - Boolean status indicating success or failure of email sending
 *
 * File: app/services/impl/EmailServicesImpl.php
 */
namespace App\Services\Impl;

use App\Services\EmailServices;
use App\Email\EmailTemplateRenderer;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Domain\Entity\Book;
use DateTime;

require __DIR__ . '/../../../vendor/autoload.php';
$config = require __DIR__ . '/../../../core/config.php';

class EmailServicesImpl implements EmailServices {
    private string $smtpUsername;
    private string $smtpPassword;
    private EmailTemplateRenderer $render;
    private string $smtpHost;
    private int $smtpPort;
    private string $defaultFrom;

    public function __construct(array $config, EmailTemplateRenderer $render) {
        $this->smtpUsername = $config['email']['gmail']['username'];
        $this->smtpPassword = $config['email']['gmail']['password'];
        $this->smtpHost = $config['email']['gmail']['host'] ?? 'smtp.gmail.com';
        $this->smtpPort = $config['email']['gmail']['port'] ?? 587;
        $this->defaultFrom = $config['email']['default_from'] ?? $this->smtpUsername;
        $this->render = $render;
    }

    /**
     * Sends an email using PHPMailer.
     *
     * @param string $to Recipient email address.
     * @param string $subject Subject of the email.
     * @param string $body Body content of the email.
     * @param string|null $from Sender email address (optional).
     * @return bool True if the email was sent successfully, false otherwise.
     */
    private function sendEmail(string $to, string $subject, string $body, ?string $from = null): bool {
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = $this->smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->smtpUsername;
            $mail->Password   = $this->smtpPassword;
            $mail->SMTPSecure = $this->smtpPort === 587
            ? PHPMailer::ENCRYPTION_STARTTLS
            : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $this->smtpPort;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($from ?? $this->defaultFrom, 'Library');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;

        } catch (Exception $e) {
            echo "Error while sending: {$mail->ErrorInfo}\n";
            return false;
        }
    }

    /**
     * Sends a verification email with a code.
     *
     * @param string $to Recipient email address.
     * @param string $userName Name of the user.
     * @param string $code Verification code.
     * @param int $expiresMinutes Expiration time in minutes.
     * @return bool True if the email was sent successfully, false otherwise.
     */
    public function sendVerificationEmail(string $to, string $userName, string $code, int $expiresMinutes = 10): bool {
        $body = $this->render->render('verify_email', [
            'userName' => $userName,
            'code' => $code,
            'expires' => $expiresMinutes,
        ]);
        return $this->sendEmail($to, 'Library - Verify your email', $body);
    }

    /**
     * Sends a reset password email with a token link.
     *
     * @param string $to Recipient email address.
     * @param string $userName Name of the user.
     * @param string $token Reset password token.
     * @param int $expiresMinutes Expiration time in minutes.
     * @return bool True if the email was sent successfully, false otherwise.
     */
    public function sendResetPasswordEmail(string $to, string $userName, string $token, int $expiresMinutes = 60): bool {
        $resetLink = "http://localhost:5500/frontend/pages/reset-password.html?token=$token";
        $body = $this->render->render('reset_password', [
            'userName' => $userName,
            'resetLink' => $resetLink,
            'expires' => $expiresMinutes,
        ]);
        return $this->sendEmail($to, 'Library - Reset your password', $body);
    }

    /**
     * Sends a reservation confirmation email for a book.
     *
     * @param string $to Recipient email address.
     * @param string $userName Name of the user.
     * @param Book $book The reserved book entity.
     * @param DateTime $expiresAt Expiration date and time of the reservation.
     * @return bool True if the email was sent successfully, false otherwise.
     */
    public function sendReservationBook(string $to, string $userName, Book $book, DateTime $expiresAt): bool {
        $body = $this->render->render('reservation_book', [
            'userName' => $userName,
            'book'=> $book,
            'expiresAt' => $expiresAt
        ]);

        return $this->sendEmail($to, 'Library - Reservation system', $body);
    }

    /**
     * Sends an email notifying the user about the extension of their book reservation.
     *
     * @param string $to Recipient email address.
     * @param string $userName Name of the user.
     * @param int $reservationId The ID of the reservation.
     * @param string $isbn The ISBN of the reserved book.
     * @param DateTime $newDate The new expiration date of the reservation.
     * @return bool True if the email was sent successfully, false otherwise.
     */
    public function sendExtendReservation(string $to, string $userName, int $reservationId, string $isbn, DateTime $newDate): bool {
        $body = $this->render->render('reservation_extend',[
            'userName' => $userName,
            'reservationId' => $reservationId,
            'isbn' => $isbn,
            'dueDate' => $newDate,
        ]);

        return $this->sendEmail($to, 'Library - Book extending service', $body);
    }

    /**
     * Sends an email notifying the user about the cancellation of their book reservation.
     *
     * @param string $to Recipient email address.
     * @param string $userName Name of the user.
     * @param int $reservationId The ID of the reservation.
     * @return bool True if the email was sent successfully, false otherwise.
     */
    public function sendCancelReservation(string $to, string $userName, int $reservationId): bool {
        $body = $this->render->render('reservation_cancel',[
            'userName' => $userName,
            'reservationId' => $reservationId,
        ]);

        return $this->sendEmail($to, 'Library - Book cancel service', $body);
    }
}
