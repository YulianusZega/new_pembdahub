<?php

namespace App\Exceptions;

/**
 * Thrown when a WhatsApp message template is not found.
 */
class TemplateNotFoundException extends WhatsAppException
{
    public function __construct(string $templateName)
    {
        parent::__construct("Template '{$templateName}' not found in whatsapp-templates config.");
    }
}
