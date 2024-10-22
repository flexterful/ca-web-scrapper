<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)

    ->ignoreDotFiles(true)
    ->ignoreVCS(true)

    ->exclude('bin')
    ->exclude('bootstrap')
    ->exclude('config')
    ->exclude('docker')
    ->exclude('public')
    ->exclude('var')
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'single_space'],
        'blank_line_before_statement' => ['statements' => ['return']],
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'psr_autoloading' => true, // PSR4
        'single_quote' => true,
    ])
    ->setFinder($finder)
;
