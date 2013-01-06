<?php

/**
 * Custom error messages for different form types.
 *
 * External messages are usually grouped and showed together at the top of the
 * form, while inline error messages on the other hand are shown underneath
 * the html field.
 *
 * Available tags to customize the error messages: <form_type>, <name>, <label>,
 * <value>, <value_type>
 *
 * @see todo diffrent possible error messages for not_blank
 */
return array(
    'not_empty' => array(
        'external' => '"<label>" is a required value.',
        'inline'   => 'This is a required field.',
    ),

    'external' => array(
        'alpha'         => ':field must contain only letters',
        'alpha_dash'    => ':field must contain only numbers, letters and dashes',
        'alpha_numeric' => ':field must contain only letters and numbers',
        'color'         => ':field must be a color',
        'credit_card'   => ':field must be a credit card number',
        'date'          => ':field must be a date',
        'decimal'       => ':field must be a decimal with :param2 places',
        'digit'         => ':field must be a digit',
        'email'         => ':field must be a email address',
        'email_domain'  => ':field must contain a valid email domain',
        'equals'        => ':field must equal :param2',
        'exact_length'  => ':field must be exactly :param2 characters long',
        'in_array'      => ':field must be one of the available options',
        'ip'            => ':field must be an ip address',
        'matches'       => ':field must be the same as :param2',
        'min_length'    => ':field must be at least :param2 characters long',
        'max_length'    => ':field must not exceed :param2 characters long',
        'not_empty'     => '"{{label}}" is a required value.',
        'numeric'       => ':field must be numeric',
        'phone'         => ':field must be a phone number',
        'range'         => ':field must be within the range of :param2 to :param3',
        'regex'         => ':field does not match the required format',
        'url'           => ':field must be a url',
    ),
    'inline' => array(
        'alpha'         => ':field must contain only letters',
        'alpha_dash'    => ':field must contain only numbers, letters and dashes',
        'alpha_numeric' => ':field must contain only letters and numbers',
        'color'         => ':field must be a color',
        'credit_card'   => ':field must be a credit card number',
        'date'          => ':field must be a date',
        'decimal'       => ':field must be a decimal with :param2 places',
        'digit'         => ':field must be a digit',
        'email'         => ':field must be a email address',
        'email_domain'  => ':field must contain a valid email domain',
        'equals'        => ':field must equal :param2',
        'exact_length'  => ':field must be exactly :param2 characters long',
        'in_array'      => ':field must be one of the available options',
        'ip'            => ':field must be an ip address',
        'matches'       => ':field must be the same as :param2',
        'min_length'    => ':field must be at least :param2 characters long',
        'max_length'    => ':field must not exceed :param2 characters long',
        'not_empty'     => 'This is a required field.',
        'numeric'       => ':field must be numeric',
        'phone'         => ':field must be a phone number',
        'range'         => ':field must be within the range of :param2 to :param3',
        'regex'         => ':field does not match the required format',
        'url'           => ':field must be a url',
    )
);
