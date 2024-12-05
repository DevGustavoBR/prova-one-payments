<?php 

namespace BackEnd\Src\Services;

class CartaoCredito 
{

    private const CARTOES_ACEITOS = [
      'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
      'mastercard' => '/^5[1-5][0-9]{14}$/',
      'amex' => '/^3[47][0-9]{13}$/',
      'discover' =>  '/^65[4-9][0-9]{13}|64[4-9][0-9]{13}|6011[0-9]{12}|(622(?:12[6-9]|1[3-9][0-9]|[2-8][0-9][0-9]|9[01][0-9]|92[0-5])[0-9]{10})$/',
      'diners_club' => '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',
      'jcb'=> '/^(?:2131|1800|35[0-9]{3})[0-9]{11}$/'
    ];


    private function validarLuhn(string $numero)
    {
       $soma = 0;
       $dobrar = false;
        
        for($i = strlen($numero) - 1; $i >= 0; $i--){
          $digito = (int) $numero[$i];
            if ($dobrar) {
              $digito *= 2;
              if ($digito > 9) {
                  $digito -= 9;
              }
          }

          $soma += $digito;
          $dobrar = !$dobrar;
        }

        return $soma % 10 === 0; 
    }

    private function cartaoAceito(string $numero)
    {
          foreach (self::CARTOES_ACEITOS as $regex) {
            if (preg_match($regex, $numero)) {
                return true;
            }
        }

        return false;
    }


    public function validarCartao(string $numero)
    {
       
        $numero = preg_replace('/\D/', '', $numero);

        if(!$this->validarLuhn($numero)){
           return false;
        }

        if (!$this->cartaoAceito($numero)) {
          return false;
      }

      return true;

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