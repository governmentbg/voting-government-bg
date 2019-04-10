<?php

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@PSR2'                                         => true,
        'array_syntax'                                  => ['syntax' => 'short'],
        'binary_operator_spaces'                        => ['align_double_arrow' => true],
        'cast_spaces'                                   => true,
        'concat_space'                                  => ['spacing' => 'one'],
        'function_typehint_space'                       => true,
        'include'                                       => true,
        'lowercase_cast'                                => true,
        'method_separation'                             => true,
        'native_function_casing'                        => true,
        'no_blank_lines_after_class_opening'            => true,
        'no_blank_lines_after_phpdoc'                   => true,
        'no_empty_comment'                              => true,
        'no_empty_phpdoc'                               => true,
        'no_empty_statement'                            => true,
        'no_leading_namespace_whitespace'               => true,
        'no_mixed_echo_print'                           => ['use' => 'echo'],
        'no_multiline_whitespace_around_double_arrow'   => true,
        'no_singleline_whitespace_before_semicolons'    => true,
        'no_spaces_around_offset'                       => true,
        'no_trailing_comma_in_list_call'                => true,
        'no_trailing_comma_in_singleline_array'         => true,
        'no_unused_imports'                             => true,
        'no_whitespace_before_comma_in_array'           => true,
        'normalize_index_brace'                         => true,
        'object_operator_without_whitespace'            => true,
        'phpdoc_align'                                  => true,
        'phpdoc_indent'                                 => true,
        'short_scalar_cast'                             => true,
        'single_quote'                                  => true,
        'ternary_operator_spaces'                       => true,
        'trailing_comma_in_multiline_array'             => true,
        'trim_array_spaces'                             => true,
        'unary_operator_spaces'                         => true,
        'whitespace_after_comma_in_array'               => true,
        'hash_to_slash_comment'                         => true,
        'phpdoc_to_comment'                             => true,
        'phpdoc_annotation_without_dot'                 => true,
        'no_unneeded_control_parentheses'               => true,
        'no_unused_imports'                             => true,
        'no_useless_else'                               => true,
        'no_spaces_inside_parenthesis'                  => true,
    ))
    ->setIndent("    ")
    ->setLineEnding("\n")
;