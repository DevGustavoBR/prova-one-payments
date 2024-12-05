<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import BotaoProcessarPagamentoAxios from '../BotaoProcessarPagamentoAxios.vue';
import ErrorComponent from '@/components/ErrorComponent.vue';
import { urlAPi } from '../../ApiUrl';

const props = defineProps({
  transacaoId: {
    type: String,
    required: true
  }
});

const detalhes = ref({});
const loading = ref(true);
const error = ref(null);
const router = useRouter();

const transacaoId = props.transacaoId;
const url = `${urlAPi}transacao/processar/pagamento/pix/${props.transacaoId}`;

const apiTransacao = async () => {
  try{

    const response = await axios.get(`${urlAPi}transacao/${transacaoId}`);

    if(response.data.status === 200){



      const data = response.data;

      if(data.dados.detalhes.metodo_pagamento !== "PIX"){
        error.value = "Essa transação não pertence ao metodo pix"
        router.push(`/checkout/pagamento/cartao-credito/${transacaoId}`);
        return;
      }

      if(data.dados.status !== 'pedente'){
         error.value = 'Essa transação já foi processada';
         return;
      }

      detalhes.value = {
        status: data.dados.status,
        cliente: data.dados.detalhes.cliente,
        produto: data.dados.detalhes.produto,
        valor: data.dados.detalhes.valor,
        metodo_pagamento: data.dados.detalhes.metodo_pagamento,
        pix_payload: data.dados.detalhes.pix_payload,
        qr_code: data.dados.detalhes.qr_code
      }

    }else{


      error.value = response.data.message;
      return;


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


  <div v-if="!error" class="font-[sans-serif] bg-white ">
      <div class="max-lg:max-w-xl mx-auto w-full">
        <div class="grid lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 max-lg:order-1 p-6 !pr-0 max-w-4xl mx-auto w-full">
            <div class="text-center max-lg:hidden">
              <h2 class="text-3xl font-extrabold text-gray-800 inline-block border-b-2 border-gray-800 pb-1">Checkout</h2>
            </div>


            <div class="text-center max-lg:hidden py-5">
              <ol>
                <li>Atenção, é apenas uma simulação</li>
                <li>Essa chave e Qrcode não funcionam, está apenas para simular o metodo de pagamento 'PIX'</li>
              </ol>
              <span></span>
            </div>

              <div class="py-2">
                <h2 class="text-xl font-bold text-gray-800">Qrcode Pix</h2>
               <img :src="detalhes.qr_code" alt="QR Code Pix" />
              </div>

              <div class="mt-16">
                <h2 class="text-xl font-bold text-gray-800">Chave Pix</h2>
                 <p class="font-bold text-gray-800">{{ detalhes.pix_payload }}</p>
              </div>

              <div class="flex flex-wrap gap-4 mt-8">
                <BotaoProcessarPagamentoAxios
                type="button"
                class="min-w-[150px] px-6 py-3.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                :url="url"
                label="Processar Pagamento"
                />
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



