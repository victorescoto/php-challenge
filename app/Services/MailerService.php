<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailerService
{
    public function __construct(
        private PHPMailer $mailer,
        private TemplateService $templateService
    ) {}

    public function sendEmail(
        string $email,
        string $subject,
        string $message,
        ?string $plainMessage = null
    ): void {
        $this->mailer->setFrom('some-email@example.com', 'Stock Staff');
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($email);
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $message;

        if ($plainMessage) {
            $this->mailer->AltBody = $plainMessage;
        }

        $result = $this->mailer->send();

        if (!$result) {
            throw new Exception('Mailer Error: ' . $this->mailer->ErrorInfo);
        }
    }

    public function sendStockEmail(string $email, string $stockCode, array $stockData): void
    {
        $stockData['date'] = sprintf('%sT%sZ', $stockData['date'], $stockData['time']);
        unset($stockData['time'], $stockData['volume']);

        $mailParams = [
            'stockCode' => $stockCode,
            'stockData' => $stockData
        ];

        $emailContent = $this->templateService->render('stock_email.php', $mailParams);
        $emailPlainContent = $this->templateService->render('stock_email_plain.php', $mailParams);

        $this->sendEmail(
            email: $email,
            subject: "Stock data for $stockCode received",
            message: $emailContent,
            plainMessage: $emailPlainContent
        );
    }
}
