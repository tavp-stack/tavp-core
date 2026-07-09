<?php

// TAVP Core — PHP-CS-Fixer configuration.
// Keeps code in "Human Style": readable, consistent, no AI fingerprints.

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
        'blank_line_after_namespace' => true,
        'class_attributes_separation' => ['elements' => ['method' => 'one']],
        'phpdoc_to_comment' => false,
    ])
    ->setFinder($finder);
