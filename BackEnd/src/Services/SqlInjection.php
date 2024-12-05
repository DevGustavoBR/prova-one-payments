<?php 

namespace App\Services;

class SqlInjection 
{

    public function sanitizaInput(string $input): string
    {
        // Remover tags HTML e evitar a preservação de atributos perigosos
        $input = strip_tags($input, '<b><i><u><strong><em>'); // Permite tags seguras, se necessário
    
        // Usar htmlspecialchars para escapar caracteres especiais de uma maneira segura
        // O parâmetro ENT_QUOTES converte tanto aspas simples quanto duplas
        // O parâmetro ENT_NOQUOTES mantém as aspas intactas, se necessário
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    
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