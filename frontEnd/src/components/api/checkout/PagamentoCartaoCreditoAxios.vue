<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import BotaoProcessarPagamentoAxios from '../BotaoProcessarPagamentoAxios.vue';
import ErrorComponent from '@/components/ErrorComponent.vue';
import { urlAPi } from '../../ApiUrl';

import {cartao, validade, cvv, erros, validaFormulario, mascaraCartao, mascaraData, mascaraCVV } from '../../ValidadarCartao'

const props = defineProps({
  transacaoId: {
    type: String,
    required: true
  }
});


const detalhes = ref({});
const loading = ref(true);
const error = ref(null);
const transacaoId = props.transacaoId;
const router = useRouter();

const formValido = ref(false);

const verificarFormulario = () => {
  validaFormulario(); // Chama a função de validação
  formValido.value = !erros.value.cartao && !erros.value.validade && !erros.value.cvv && cartao.value && validade.value && cvv.value;
};


watch([cartao, validade, cvv], verificarFormulario);

const url = `${urlAPi}transacao/processar/pagamento/cartao-credito/${transacaoId}`

const apiTransacao = async () => {
  try{

    const response = await axios.get(`${urlAPi}transacao/${transacaoId}`);

    if(response.data.status === 200){

      const data = response.data;

      if(data.dados.detalhes.metodo_pagamento === "PIX"){
        error.value = "Essa transação não pertence ao metodo pix"
        router.push(`/checkout/pagamento/pix/${transacaoId}`);
        return;
      }

      if(data.dados.status !== 'pedente'){
         error.value = 'Essa transação já foi processada';
      }

        detalhes.value = {
        status: data.dados.status,
        cliente: data.dados.detalhes.cliente,
        produto: data.dados.detalhes.produto,
        valor: data.dados.detalhes.valor,
        metodo_pagamento: data.dados.detalhes.metodo_pagamento,
      }

    }else{

      error.value = response.data.message;


    }

  }catch(err){

    error.value = "Erro ao buscar os dados da transação.";
    console.error(err);

  }finally{

    loading.value = false;
  }

}


onMounted(apiTransacao);

</script>

<template>

<ErrorComponent :error="error"/>

  <div v-if="!error" class="font-[sans-serif] bg-white">
      <div class="max-lg:max-w-xl mx-auto w-full">
        <div class="grid lg:grid-cols-3 gap-6">

          <div class="lg:col-span-2 max-lg:order-1 p-6 !pr-0 max-w-4xl mx-auto w-full">



            <div class="text-center max-lg:hidden">
              <h2 class="text-3xl font-extrabold text-gray-800 inline-block border-b-2 border-gray-800 pb-1">Checkout</h2>
            </div>

            <div class="text-center max-lg:hidden py-5">
              <ol>
                <li>Atenção, é apenas uma simulação</li>
                <li>Utilize alguma plataforma para gerar os dados para cartão de crédito válido, como a 4devs.com.br</li>
                <li>Não coloque seus dados reais aqui</li>
              </ol>
              <span></span>
            </div>



              <div class="py-5">
                <h2 class="text-xl font-bold text-gray-800">Informações do cartão</h2>

                <div class="grid sm:grid-cols-2 gap-8 mt-8">
                  <div>
                    <label v-if="erros.cartao" for="">{{ erros.cartao }}</label>
                    <input type="text" placeholder="Número Cartão"
                      v-model="cartao"
                      @input="mascaraCartao"
                      class="px-2 pb-2 bg-white text-gray-800 w-full text-sm border-t-0 border-l-0 border-r-0 border-b focus:border-blue-600 outline-none" />
                  </div>
                  <div>
                    <label v-if="erros.validade" for="">{{ erros.validade }}</label>
                    <input type="text" placeholder="Data de expiração"
                      v-model="validade"
                      @input="mascaraData"
                      class="px-2 pb-2 bg-white text-gray-800 w-full text-sm border-t-0 border-l-0 border-r-0 border-b focus:border-blue-600 outline-none" />
                  </div>
                  <div>
                    <label v-if="erros.cvv" for="">{{ erros.cvv }}</label>
                    <input type="text" placeholder="CVV"
                      v-model="cvv"
                      @input="mascaraCVV"
                      class="px-2 pb-2 bg-white text-gray-800 w-full text-sm border-t-0 border-l-0 border-r-0 border-b focus:border-blue-600 outline-none" />
                  </div>
                </div>
              </div>


              <div class="flex flex-wrap gap-4 mt-8">
                <BotaoProcessarPagamentoAxios
                type="button"
                :url = "url"
                :cartao="cartao"
                :validade="validade"
                :cvv="cvv"
                :label="'Processar pagamento R$ ' + detalhes.valor"
                :disabled="!formValido"
                class="min-w-[150px] px-6 py-3.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700" />
              </div>
          </div>

          <div class="bg-gray-100 p-6 rounded-md">
            <h2 class="text-4xl font-extrabold text-gray-800">R$ {{ detalhes.valor  }}</h2>

            <ul class="text-gray-800 mt-8 space-y-4">
              <li class="flex flex-wrap gap-4 text-sm">{{ detalhes.produto }} <span class="ml-auto font-bold">R$ {{ detalhes.valor  }}</span></li>
              <li class="flex flex-wrap gap-4 text-sm">Descontos <span class="ml-auto font-bold">R$ 00.00</span></li>
              <li class="flex flex-wrap gap-4 text-sm">Taxas <span class="ml-auto font-bold">R$ 00.00</span></li>
              <li class="flex flex-wrap gap-4 text-sm font-bold border-t-2 pt-4">Total <span class="ml-auto">R$ {{ detalhes.valor  }}</span></li>
            </ul>
          </div>

        </div>
      </div>
    </div>
</template>





