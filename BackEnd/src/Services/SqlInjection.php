<?php 

namespace App\Services;

class SqlInjection 
{

    public function validarInput($input, string $type = 'string')
    {
        // Verifica se o input não está vazio
        if(empty($input)){
           return false;   
        }    

         // Array de validações
        $validarType = [
           'string' => fn($input) => is_string($input),
           'int' => fn($input) => is_int($input),
           'email' => fn($input) => filter_var($input, FILTER_VALIDATE_EMAIL),
           'url' => fn($input) => filter_var($input, FILTER_VALIDATE_URL),
        ];

        return isset($validarType[$type]) ? $validarType[$type]($input) : false;
        
    }


    public function sanitizaInput(string $input)
    {
        // Remover tags HTML
        $input = strip_tags($input);

        // Usar filter_var para escapar caracteres especiais de uma maneira segura
        // Caso precise sanitizar para prevenir XSS, use o filtro FILTER_SANITIZE_STRING
        $input =  filter_var($input, FILTER_SANITIZE_STRING);

        return $input;
    }

    public function sanitizaSqlInput(string $input)
    {

        // Aqui não vamos usar addslashes() ou algo similar
        // Pois o ideal é usar consultas preparadas do Doctrine, que já lidam com a proteção contra SQL Injection
        // Caso realmente precise "sanitizar", você pode limitar os caracteres permitidos.

        // Exemplo: remover caracteres não alfanuméricos, exceto alguns símbolos permitidos

        $input = preg_replace('/[^a-zA-Z0-9_\- ]/', '', $input);
        return $input;  
    }
        


}