<script setup>
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
  url: {type: String, required: true},
  label: { type: String, required: true},
  buttonClass: {type: String, required: true},
  cartao: { type: String, default: null},
  validade: { type: String, default: null},
  cvv: { type: String, default: null },
  disabled: { type: Boolean, default: false }
});

const emit = defineEmits(['response']);
const router = useRouter();

const processarPagamento = async () => {
  try{

    const requestDados = props.cartao && props.validade && props.cvv ? {
       cartao: props.cartao,
        validade: props.validade,
        cvv: props.cvv,
    } : null

    const response = await axios.put(props.url, requestDados);

    if(response.data.status === 200){
        emit('response', response.data);
        const idTransacao =  response.data.idTransacao;
        await Swal.fire({
            icon: 'success',
            title: 'Pagamento Processado',
            text: 'Redirecionando para pagina de Detalhes da transação',
            timer: 2000,
            showConfirmButton: false,
        });
        router.push({name:"detalhes", params: {id: idTransacao}});
      }else{
        console.error(`Erro: ${response.data.message}`);
        await Swal.fire({
          icon: 'error',
          title: 'Erro!',
          text: response.data.message,
        });
      }


  }catch(error){
    console.error(`Erro ao fazer a requisição para ${props.url}:`, error)
    await Swal.fire({
      icon: 'error',
      title: 'Erro na requisição!',
      text: 'Algo deu errado ao processar sua solicitação.',
    });
  }
}

</script>

<template>
  <button :class="props.buttonClass" @click="processarPagamento" :disabled="disabled">{{ props.label }}</button>
</template>



