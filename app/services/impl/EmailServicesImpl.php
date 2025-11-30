<?php
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

    public function sendVerificationEmail(string $to, string $userName, string $code, int $expiresMinutes = 10): bool {
        $body = $this->render->render('verify_email', [
            'userName' => $userName,
            'code' => $code,
            'expires' => $expiresMinutes,
        ]);
        return $this->sendEmail($to, 'Library - Verify your email', $body);
    }

    public function sendResetPasswordEmail(string $to, string $userName, string $token, int $expiresMinutes = 60): bool {
        $resetLink = "http://localhost:5500/frontend/pages/reset-password.html?token=$token";
        $body = $this->render->render('reset_password', [
            'userName' => $userName,
            'resetLink' => $resetLink,
            'expires' => $expiresMinutes,
        ]);
        return $this->sendEmail($to, 'Library - Reset your password', $body);
    }

    public function sendReservationBook(string $to, string $userName, Book $book, DateTime $expiresAt): bool {
        $body = $this->render->render('reservation_book', [
            'userName' => $userName,
            'book'=> $book,
            'expiresAt' => $expiresAt
        ]);

        return $this->sendEmail($to, 'Library - Reservation system', $body);
    }

    public function sendExtendReservation(string $to, string $userName, int $reservationId, string $isbn, DateTime $newDate): bool {
        $body = $this->render->render('reservation_extend',[
            'userName' => $userName,
            'reservationId' => $reservationId,
            'isbn' => $isbn,
            'dueDate' => $newDate,
        ]);

        return $this->sendEmail($to, 'Library - Book extending service', $body);
    }

    public function sendCancelReservation(string $to, string $userName, int $reservationId): bool {
        $body = $this->render->render('reservation_cancel',[
            'userName' => $userName,
            'reservationId' => $reservationId,
        ]);

        return $this->sendEmail($to, 'Library - Book cancel service', $body);
    }
}
