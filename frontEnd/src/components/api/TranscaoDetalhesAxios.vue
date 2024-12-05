<script setup>

import { ref, onMounted } from 'vue';
import axios from 'axios';
import InputField from '@/components/InputField.vue';
import ErrorComponent from '@/components/ErrorComponent.vue';
import { urlAPi } from '../ApiUrl';

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

const apiTransacao = async () => {
  try{

    const response = await axios.get(`${urlAPi}transacao/${transacaoId}`);

    if(response.data.status === 200){

      const data = response.data;

      const formatarDataHora = (dataISO) => {
        const data = new Date(dataISO);
        return new Intl.DateTimeFormat('pt-BR', {
          dateStyle: 'short',
          timeStyle: 'medium',
        }).format(data);
      };

      detalhes.value = {
        transacao: data.dados.idTransacao,
        status: data.dados.status,
        cliente: data.dados.detalhes.cliente,
        produto: data.dados.detalhes.produto,
        valor: data.dados.detalhes.valor,
        servico: data.dados.detalhes.servico,
        metodo_pagamento: data.dados.detalhes.metodo_pagamento,
        dataIncial:formatarDataHora(data.dados.createdAt),
        dataFinal: formatarDataHora(data.dados.updatedAt)
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
              <h2 class="text-3xl font-extrabold text-gray-800 inline-block border-b-2 border-gray-800 pb-1">Transação {{ detalhes.status}} </h2>
            </div>

              <div>
                <h2 class="text-xl font-bold text-gray-800">Detalhes</h2>

                <div class="grid sm:grid-cols-2 gap-8 mt-8">

                  <div>
                    <InputField
                    label="Transação ID"
                    :modelValue="detalhes.transacao"
                    :disabled="true"
                    />
                  </div>

                  <div>
                    <InputField
                    label="Cliente"
                    :modelValue="detalhes.cliente"
                    :disabled="true"
                    />
                  </div>

                  <div>
                    <InputField
                    label="Produto"
                    :modelValue="detalhes.produto"
                    :disabled="true"
                    />
                  </div>

                  <div>
                    <InputField
                    label="Valor"
                    :modelValue="detalhes.valor"
                    :disabled="true"
                    />
                  </div>

                  <div>
                    <InputField
                    label="Serviço"
                    :modelValue="detalhes.servico"
                    :disabled="true"
                    />
                  </div>


                  <div>
                    <InputField
                    label="Metodo de Pagamento"
                    :modelValue="detalhes.metodo_pagamento"
                    :disabled="true"
                    />
                  </div>

                  <div>
                    <InputField
                    label="Data hora do pedido"
                    :modelValue="detalhes.dataIncial"
                    :disabled="true"
                    />
                  </div>


                  <div v-if="detalhes.status !== 'pedente'">
                    <InputField
                    label="Data hora da conclusão"
                    :modelValue="detalhes.dataFinal"
                    :disabled="true"
                    />
                  </div>


                </div>
              </div>


              <div class="flex flex-wrap gap-4 mt-8">
                <RouterLink v-if="detalhes.metodo_pagamento === 'PIX' && detalhes.status === 'pedente'" :to="'/checkout/pagamento/pix/' + detalhes.transacao" class="min-w-[150px] px-6 py-3.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">Pagina de Pagamento</RouterLink>
                <RouterLink v-if="detalhes.metodo_pagamento === 'Cartão de Crédito' && detalhes.status === 'pedente'" :to="'/checkout/pagamento/cartao-credito/' + detalhes.transacao" type="button" class="min-w-[150px] px-6 py-3.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">Pagina de Pagamento</RouterLink>
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



