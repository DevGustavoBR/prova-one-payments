
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ErrorComponent from '@/components/ErrorComponent.vue';
import { urlAPi } from '../ApiUrl';


const props = defineProps({
  produtoId: {
    type: String,
    required: true
  }
});

const produto = ref({
  nome: '',
  preco: '',
  descricao: '',
  imagem1: '',
  imagem2: '',
  imagem3: '',
  imagem4: '',
});

const loading = ref(true);
const error = ref(null);

const idProduto = props.produtoId

const url = `/checkout/metodo-pagamento/${idProduto}`;

const apiProduto = async () =>{

  try{
    const response = await axios.get(`${urlAPi}produtos/${idProduto}`);

    if(response.data.status === 200){
      const data = response.data.data;

      produto.value = {
        nome: data.nome,
        preco: `R$ ${data.preco}`,
        descricao: data.descricao,
        imagem1: data.imagemProduto,
        imagem2: data.imagemProduto2,
        imagem3: data.imagemProduto3,
        imagem4: data.imagemProduto4,

      }
    }else{
       console.error(`Error ${response.data.status}: ${response.data.message}`);
       error.value = response.data.message;
    }

  }catch(err){
    error.value = "Erro ao buscar o produto:";
    console.log(err);
  }finally{
    loading.value = false;
  }

}


onMounted(() => {
  console.log("Componente montado");
  apiProduto();
});
</script>


<template>


<ErrorComponent :error="error"/>


<div v-if="!error">

  <div class="p-4 mx-auto lg:max-w-7xl sm:max-w-full">
  <h2 class="text-4xl font-extrabold  text-gray-800 mb-12">Produto</h2>
</div>


        <div class="font-[sans-serif]">

            <div class="grid items-start grid-cols-1 md:grid-cols-2 p-4 gap-12 max-w-6xl mx-auto">
                <div class="grid grid-cols-2 md:sticky top-0 gap-3">
                    <div>
                        <img :src="produto.imagem1" :alt="produto.nome" class="w-full h-full object-cover object-top rounded-md" />
                    </div>
                    <div>
                        <img :src="produto.imagem2" :alt="produto.nome" class="w-full h-full object-cover object-top rounded-md" />
                    </div>
                    <div>
                        <img :src="produto.imagem3" :alt="produto.nome" class="w-full h-full object-cover object-top rounded-md" />
                    </div>
                    <div>
                        <img :src="produto.imagem4" :alt="produto.nome" class="w-full h-full object-cover object-top rounded-md" />
                    </div>
                </div>

                <div class="max-lg:max-w-2xl">
                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-800">{{ produto.nome }}</h2>
                    </div>


                    <div class="mt-8">
                        <p class="text-gray-800 text-4xl font-bold">{{ produto.preco }}</p>
                    </div>

                    <div class="mt-8 space-y-4 max-w-xs">
                        <RouterLink :to="url" class="w-full px-4 py-3 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-md">Comprar</RouterLink>
                    </div>

                    <div class="mt-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Descrição do Produto</h3>
                            <p class="text-sm text-gray-500 mt-4">{{ produto.descricao }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</div>




</template>


