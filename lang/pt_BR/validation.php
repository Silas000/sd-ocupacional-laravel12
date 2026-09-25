<?php

// Lang override for Laravel validation messages - pt_BR
return [
    'required' => 'O campo :attribute é obrigatório.',
    'email' => 'O campo :attribute deve ser um e-mail válido.',
    'unique' => 'O valor do campo :attribute já está sendo utilizado.',
    'confirmed' => 'A confirmação de :attribute não coincide.',
    'password' => 'A senha fornecida está incorreta.',
    'date' => 'O campo :attribute não é uma data válida.',
    'max' => [
        'string' => 'O campo :attribute não pode ser maior que :max caracteres.',
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
    ],
    'min' => [
        'string' => 'O campo :attribute deve ter no mínimo :min caracteres.',
        'numeric' => 'O campo :attribute deve ser no mínimo :min.',
    ],
    'in' => 'O valor selecionado para :attribute é inválido.',
    'exists' => 'O valor selecionado para :attribute é inválido.',
    'same' => 'Os campos :attribute e :other devem ser iguais.',
    'different' => 'Os campos :attribute e :other devem ser diferentes.',
    'array' => 'O campo :attribute deve ser um array.',
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
];
