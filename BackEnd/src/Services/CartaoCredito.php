<?php 

namespace BackEnd\Src\Services;

class CartaoCredito 
{

    private const CARTOES_ACEITOS = [
      'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
      'mastercard' => '/^5[1-5][0-9]{14}$/',
      'amex' => '/^3[47][0-9]{13}$/',
    ];

    public function validarCartao(string $numero)
    {
       
        $numero = preg_replace('/\D/', '', $numero);

        $soma = 0;
        $dobrar = false;

        for($i = strlen($numero) - 1; $i >= 0; $i--){
            
            $digito = (int) $numero[$i];
            
            if($dobrar){
                
                $digito *=2;
                
                if($digito > 9){
                   $digito -= 9;
                }
            }

             $soma += $digito;
             $dobrar = !$dobrar;
        }

        $valida = $soma % 10 === 0;

        $aceito = false;

        foreach(self::CARTOES_ACEITOS as $regex){
            if(preg_match($regex, $numero)){
              $aceito = true;
              break;
            }
        }

        return $valida && $aceito;

    }

   public function validarCVV(string $numeroCartao, string $cvv)
   {

       $numeroCartao = preg_replace('/\D/', '', $numeroCartao);
       $cvv = preg_replace('/\D/', '', $cvv);

       if(preg_match(self::CARTOES_ACEITOS['amex'], $numeroCartao)){
         return preg_match('/^\d{4}$/', $cvv);
       }

       return preg_match('/^\d{3}$/', $cvv);
   } 

   public function validarValidade(string $validade)
   {
    return preg_match('/^\d{2}\/\d{2}$/', $validade);
   }

}