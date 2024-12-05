<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import BotaoAxios from '../BotaoAxios.vue';
import InputField from '@/components/InputField.vue';


const props = defineProps({
  produtoId: {
    type: String,
    required: true
  }
});

const produto = ref({
  nome: '',
  preco: '',
});


  const idProduto = props.produtoId;

  const loading = ref(true);

  const apiProduto = async () => {
    try{
        const response = await axios.get(`http://127.0.0.1:8000/api/produtos/${idProduto}`)
        if(response.data.status === 200){
          const data = response.data.data;

          produto.value = {
            nome: data.nome,
            preco: data.preco,
          }

        formData.value = {
        cliente: formData.value.cliente,
        produto: produto.value.nome,
        valor: produto.value.preco,
        servico: `Compra do produto ${produto.value.nome}`,
      };
        }else{
          console.error(`Error ${response.data.status}: ${response.data.message}`);
        }
    }catch(error){
      console.log("Erro ao buscar o produto:", error);
    }finally{
      loading.value = false;
    }
  }

  const formData = ref({
  cliente: '',
  produto: '',
  valor: '',
  servico: '',
})



const nomeValido = ref(false)


const validarNomeCliente = () => {
  const nome = formData.value.cliente.trim();

  if(!nome){
    alert("O campo nome não pode ser vazio")
    nomeValido.value = false
    return false;
  }

  const regex = /(<([^>]+)>|[^\w\sÀ-ÿáàãâéèêíïóôõöúç])/g;

  if(regex.test(nome)){
    alert("Nome inválido! Não são permitidos caracteres especiais ou tags HTML.");
    nomeValido.value = false
    return false;
  }

  nomeValido.value = true;

  return true;
}

  onMounted(() => {
  console.log("Componente montado");
  apiProduto();
  });

</script>

<template>
<div class="font-[sans-serif] bg-white">
      <div class="max-lg:max-w-xl mx-auto w-full">
        <div class="grid lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 max-lg:order-1 p-6 !pr-0 max-w-4xl mx-auto w-full">
            <div class="text-center max-lg:hidden">
              <h2 class="text-3xl font-extrabold text-gray-800 inline-block border-b-2 border-gray-800 pb-1">Checkout</h2>
            </div>

            <div>
                <h2 class="text-xl font-bold text-gray-800">Informações do produto</h2>

                <div class="grid sm:grid-cols-2 gap-8 mt-8">
                  <div>
                    <InputField
                    :label="'Nome Produto'"
                    :modelValue="produto.nome"
                    :inputClass="'px-2 pb-2 bg-white text-gray-800 w-full text-sm border-t-0 border-l-0 border-r-0 border-b focus:border-blue-600 outline-none'"
                    :disabled="true"
                    />
                  </div>
                  <div>
                  <InputField
                  :label="'Valor'"
                  :modelValue="produto.preco"
                  :inputClass="'px-2 pb-2 bg-white text-gray-800 w-full text-sm border-t-0 border-l-0 border-r-0 border-b focus:border-blue-600 outline-none'"
                  disabled="true"
                  />
                  </div>
                  <div>

                    <InputField
                  :label="'Serviço'"
                  :modelValue="formData.servico"
                  :inputClass="'px-2 pb-2 bg-white text-gray-800 w-full text-sm border-t-0 border-l-0 border-r-0 border-b focus:border-blue-600 outline-none'"
                  disabled="true"
                  />
                  </div>
                </div>
              </div>

              <div class="lg:mt-16">
                <h2 class="text-xl font-bold text-gray-800">Informação do cliente</h2>

                <div class="grid sm:grid-cols-2 gap-8 mt-8">
                  <div>
                    <InputField
                      label="Nome Completo"
                      v-model="formData.cliente"
                      type="text"
                      :inputClass="'px-2 pb-2 bg-white text-gray-800 w-full text-sm border-t-0 border-l-0 border-r-0 border-b focus:border-blue-600 outline-none'"
                      @blur="validarNomeCliente"
                      />
                  </div>
                </div>
              </div>

              <div class="mt-16">
                <h2 class="text-xl font-bold text-gray-800">Metodo de Pagamento</h2>
              </div>

              <div class="flex flex-wrap gap-4 mt-8">
                <BotaoAxios
                url="http://127.0.0.1:8000/api/transacao/pagamento/criar/pix"
                label="PIX"
                :data="formData"
                :buttonClass="'min-w-[150px] px-6 py-3.5 text-sm bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300'"
                :disabled="!nomeValido"
                />

              <BotaoAxios
               url = "http://127.0.0.1:8000/api/transacao/pagamento/criar/cartao-credito"
               label="Cartão de Credito"
               :data="formData"
               :buttonClass="'min-w-[150px] px-6 py-3.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700'"
                :disabled="!nomeValido"
              />
              </div>
          </div>

          <div class="bg-gray-100 p-6 rounded-md">
          <h2 class="text-4xl font-extrabold text-gray-800">R$ {{produto.preco}}</h2>

          <ul class="text-gray-800 mt-8 space-y-4">
            <li class="flex flex-wrap gap-4 text-sm">Descontos <span class="ml-auto font-bold">R$00.00</span></li>
            <li class="flex flex-wrap gap-4 text-sm">Taxas <span class="ml-auto font-bold">R$00.00</span></li>
            <li class="flex flex-wrap gap-4 text-sm font-bold border-t-2 pt-4">Total <span class="ml-auto">R$ {{produto.preco}}</span></li>
          </ul>
        </div>
        </div>
      </div>
    </div>
</template>



