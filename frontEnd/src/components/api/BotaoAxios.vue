<script setup>
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
  url: {type: String, required: true},
  label: { type: String, required: true},
  data: {type: Object, required: true},
  buttonClass: {type: String, required: true},
  disabled: { type: Boolean, default: false }
});

const emit = defineEmits(['response']);
const router = useRouter();

const fazerRequisicao = async () => {
  if (props.disabled) return;
  console.log('Enviando dados:', props.data);
  try{
      const response = await axios.post(props.url, props.data);


      if(response.data.status === 201){
        emit('response', response.data)
        const idTransacao = response.data.idTransacao;

        if(response.data.dados.detalhes.metodo_pagamento === "PIX"){
          await Swal.fire({
            icon: 'success',
            title: 'Transação Criada',
            text: 'Redirecionando para pagamento PIX',
            timer: 2000,
            showConfirmButton: false,
          });
          router.push({name:"pixPagamento", params: {id: idTransacao}});
        }else{
          await Swal.fire({
            icon: 'success',
            title: 'Transação Criada',
            text: 'Redirecionando para pagamento com cartão de crédito',
            timer: 2000,
            showConfirmButton: false,
          });
          router.push({name:"cartaoCreditoPagamento", params: {id: idTransacao}})
        }


      }else{
        console.error(`Erro: ${response.data.message}`);
        Swal.fire({
        icon: 'error',
        title: 'Erro!',
        text: response.data.message,
      });
      }

  }catch(error){
      console.error(`Erro ao fazer a requisição para ${props.url}:`, error)
      Swal.fire({
      icon: 'error',
      title: 'Erro na requisição!',
      text: 'Algo deu errado ao processar sua solicitação.',
    });
  }
}

</script>
<template>
  <button :class="props.buttonClass" @click="fazerRequisicao" :disabled="disabled">{{ props.label }}</button>
</template>

