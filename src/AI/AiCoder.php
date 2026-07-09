<?php

declare(strict_types=1);

namespace Tavp\AI;

/**
 * AI Coder — generate complete modules from natural language.
 */
class AiCoder
{
    public function __construct(private AiManager $ai)
    {
    }

    /**
     * Generate a complete module from description.
     */
    public function generateModule(string $description): array
    {
        $files = [];

        // Generate model
        $files['Model'] = $this->ai->generateCode(
            "Generate a PHP model class for: {$description}",
            'php'
        );

        // Generate controller
        $files['Controller'] = $this->ai->generateCode(
            "Generate a PHP controller with CRUD operations for: {$description}",
            'php'
        );

        // Generate migration
        $files['Migration'] = $this->ai->generateCode(
            "Generate a database migration for: {$description}",
            'php'
        );

        // Generate views
        $files['Index View'] = $this->ai->generateCode(
            "Generate a Volt template for listing: {$description}",
            'volt'
        );

        // Generate routes
        $files['Routes'] = $this->ai->generateCode(
            "Generate Phalcon routes for: {$description}",
            'php'
        );

        return $files;
    }

    /**
     * Generate a single file.
     */
    public function generateFile(string $description, string $type, string $language = 'php'): string
    {
        $prompt = "Generate a {$type} file for: {$description}";
        return $this->ai->generateCode($prompt, $language);
    }
}
