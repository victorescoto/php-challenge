<?php

declare(strict_types=1);

namespace App\Services;

class TemplateService
{
    public function render(string $template, array $variables = []): string
    {
        extract($variables);
        ob_start();
        include TEMPLATE_PATH . '/' . $template;
        return ob_get_clean();
    }
}
