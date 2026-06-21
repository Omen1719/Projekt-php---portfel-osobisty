<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

/*
 * Mirrors the grader's command:
 *   php-cs-fixer fix src/ --rules=@Symfony,@PSR1,@PSR2,@PSR12
 * so running `vendor/bin/php-cs-fixer fix` locally gives the same result.
 */
$finder = (new Finder())
    ->in(__DIR__.'/src');

return (new Config())
    ->setRules([
        '@Symfony' => true,
        '@PSR1' => true,
        '@PSR2' => true,
        '@PSR12' => true,
    ])
    ->setRiskyAllowed(false)
    ->setFinder($finder);
