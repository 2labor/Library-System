<?php
/*
 * Inputs:
 * - Template name and data array
 *
 * Outputs:
 * - Rendered email content as string
 *
 * File: app/email/EmailTemplateRenderer.php
 */
namespace App\Email;

class EmailTemplateRenderer
{
    private string $templatePath;

    public function __construct(string $templatePath = __DIR__ . '/templates/')
    {
        $this->templatePath = rtrim($templatePath, '/') . '/';
    }

    /**
     * Renders an email template with the given data.
     */
    public function render(string $templateName, array $data = []): string
    {
        $file = $this->templatePath . $templateName . '.php';

        if (!file_exists($file)) {
            throw new Exception("Email template not found: " . $templateName);
        }
        extract($data);
        ob_start();
        include $file;
        return ob_get_clean();
    }
}
