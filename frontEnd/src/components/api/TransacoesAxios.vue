<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ErrorComponent from '@/components/ErrorComponent.vue';
import { urlAPi } from '../ApiUrl';

const transacoes = ref([]);
const loading = ref(true);
const error = ref(null);

const apiTransacoes = async () => {

  try{

    const response = await axios.get(`${urlAPi}transacoes`);
    const data = response.data;

    if(data.status === 200){
      const formatarDataHora = (dataISO) => {
        const data = new Date(dataISO);
        return new Intl.DateTimeFormat('pt-BR', {
          dateStyle: 'short',
          timeStyle: 'medium',
        }).format(data);
      };

        transacoes.value = data.dados.map(transacoes => ({
        id: transacoes.id,
        cliente: transacoes.detalhes.cliente,
        idTransacao: transacoes.idTransacao,
        status: transacoes.status,
        dataPedido: formatarDataHora(transacoes.createdAt),
        href: `/transacao/${transacoes.idTransacao}`
       }));

    }else{
      console.error(data.message);
      error.value = data.message;

    }

  }catch(err){
    error.value = "Erro ao buscar os dados da transação.";
    console.error(err);
  }finally{
    loading.value = false;
  }

}

onMounted(apiTransacoes)


</script>



<template>

<ErrorComponent :error="error"/>


<div v-if="!error">
  <div class="text-center max-lg:hidden">
        <h2 class="text-3xl font-extrabold text-gray-800 inline-block  pb-1 py-5">Transações</h2>
  </div>
<div class="font-[sans-serif] overflow-x-auto py-5">

<table class="min-w-full bg-white">
  <thead class="bg-gray-500 whitespace-nowrap">
    <tr>
      <th class="p-4 text-left text-sm font-medium text-white">
        #
      </th>
      <th class="p-4 text-left text-sm font-medium text-white">
        ID Transação
      </th>
      <th class="p-4 text-left text-sm font-medium text-white">
        Cliente
      </th>
      <th class="p-4 text-left text-sm font-medium text-white">
        Status
      </th>
      <th class="p-4 text-left text-sm font-medium text-white">
        Data Pedido
      </th>
      <th class="p-4 text-left text-sm font-medium text-white">
       Ações
      </th>
    </tr>
  </thead>

  <tbody class="whitespace-nowrap">
    <tr class="even:bg-blue-50" v-for="transacao in transacoes" :key="transacao.id">
      <td class="p-4 text-sm text-black">
        {{  transacao.id}}
      </td>
      <td class="p-4 text-sm text-black">
        {{  transacao.idTransacao}}
      </td>
      <td class="p-4 text-sm text-black">
        {{  transacao.cliente}}
      </td>
      <td class="p-4 text-sm text-black">
        {{ transacao.status }}
      </td>
      <td class="p-4 text-sm text-black">
        {{  transacao.dataPedido }}
      </td>
      <td class="p-4">
        <RouterLink
        :to="transacao.href"
        class="min-w-[150px] px-6 py-3.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Detalhes
        </RouterLink>
      </td>
    </tr>
  </tbody>
</table>
</div>
</div>


</template>
