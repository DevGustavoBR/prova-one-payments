

import { ref } from 'vue';

export const cartao = ref('');

export const validade = ref('');

export const cvv = ref('');

export const erros = ref({ cartao: '', validade: '', cvv: '' });

export const CartoesAceitos = {

  visa: /^4[0-9]{12}(?:[0-9]{3})?$/,
  mastercard: /^5[1-5][0-9]{14}$/,
  amex: /^3[47][0-9]{13}$/,
  discover: /^65[4-9][0-9]{13}|64[4-9][0-9]{13}|6011[0-9]{12}|(622(?:12[6-9]|1[3-9][0-9]|[2-8][0-9][0-9]|9[01][0-9]|92[0-5])[0-9]{10})$/,
  diners_club: /^3(?:0[0-5]|[68][0-9])[0-9]{11}$/,
  jcb: /^(?:2131|1800|35[0-9]{3})[0-9]{11}$/
}


export function validarCartao(value){

  value  = value.replace(/\D/g, '');

    let soma = 0;
    let dobrar = false;

    for(let i = value.length - 1; i >= 0; i--){
        let digito = parseInt(value.charAt(i));
        if(dobrar){
          if((digito *= 2) > 9) digito -= 9;
        }

        soma += digito;

        dobrar = !dobrar;
    }

    const valida = soma % 10 === 0;
    let aceita = false;

    Object.keys(CartoesAceitos).forEach((key) => {
      const regex = CartoesAceitos[key];
      if(regex.test(value)){
         aceita = true;
      }
    });

    return valida && aceita;
}

export function validaCVV(CartaoCredito, cvv){

  CartaoCredito = CartaoCredito.replace(/\D/g, '');
  cvv = cvv.replace(/\D/g, '');

  if(CartoesAceitos.amex.test(CartaoCredito)){
    return /^\d{4}$/.test(cvv);
  }

  return /^\d{3}$/.test(cvv);

}

export function validaFormulario()
{
  erros.value = {cartao:'', validade: '', cvv: ''};

  if(!validarCartao(cartao.value)){
    erros.value.cartao = 'Número do cartão inválido.';
  }

  if (!/^\d{2}\/\d{2}$/.test(validade.value)) {
    erros.value.validade = 'Data de validade inválida.';
  }

  if (!validaCVV(cartao.value, cvv.value)) {
    erros.value.cvv = 'CVV inválido.';
  }

  if (!erros.value.cartao && !erros.value.validade && !erros.value.cvv) {
    alert('Cartão Válido!');
  }

}

export function mascaraCartao(){
  cartao.value = cartao.value.replace(/\D/g, '').replace(/(\d{4})(?=\d)/g, '$1 ');
}

export function mascaraData(){
   validade.value = validade.value.replace(/\D/g, '').replace(/(\d{2})(\d{1,2})/, '$1/$2').slice(0, 5);
}

export function mascaraCVV(){
  cvv.value = cvv.value.replace(/\D/g, '').slice(0, 4);
}

