<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->in('tests')
    ->in('db')
    ->in('packages')
    ->in('config')
    ->in('resources/scripts')
    ->append(
        [
            __FILE__,
            'public/index.php',
            'docker/db/wait-for-db.php',
            'bin/console',
            'phinx.php',
        ],
    )
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules(
        [
            '@PSR12' => true,
            '@PSR12:risky' => true,
            '@Symfony' => true,
            '@Symfony:risky' => true,
            '@PhpCsFixer' => true,
            '@PhpCsFixer:risky' => true,
            '@PHP56Migration:risky' => true,
            '@PHP70Migration:risky' => true,
            '@PHP71Migration:risky' => true,
            '@PHP73Migration' => true,
            '@PHP74Migration' => true,
            '@PHP74Migration:risky' => true,
            '@PHPUnit75Migration:risky' => true,
            '@PHPUnit84Migration:risky' => true,
            '@PHP82Migration' => true,
            'linebreak_after_opening_tag' => true,
            'php_unit_method_casing' => ['case' => 'snake_case'],
            'php_unit_test_class_requires_covers' => false,
            'concat_space' => ['spacing' => 'one'],
            'array_syntax' => ['syntax' => 'short'],
            'native_constant_invocation' => ['strict' => false],
            'native_function_invocation' => ['include' => ['@all']],
            'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
            'strict_comparison' => false,
            'backtick_to_shell_exec' => true,
            'void_return' => true,
            'simplified_null_return' => true,
            'declare_strict_types' => true,
            'static_lambda' => true,
            'php_unit_strict' => false,
            'php_unit_dedicate_assert' => true,
            'php_unit_dedicate_assert_internal_type' => true,
            'php_unit_namespaced' => true,
            'ternary_to_null_coalescing' => true,
            'return_assignment' => false,
            'use_arrow_functions' => false,
            'trailing_comma_in_multiline' => [
                'elements' => [
                    'arrays',
                    'arguments',
                    'parameters',
                ],
            ],
            'blank_line_before_statement' => [
                'statements' => [
                    'break',
                    'case',
                    'continue',
                    'declare',
                    'default',
                    'return',
                    'throw',
                    'try',
                ],
            ],
            'php_unit_data_provider_static' => true,
            'phpdoc_line_span' => [
                'property' => 'single',
                'method' => 'single',
                'const' => 'single',
            ],
            'multiline_whitespace_before_semicolons' => false,
            'php_unit_data_provider_name' => false,
        ],
    )
    ->setFinder($finder)
;
