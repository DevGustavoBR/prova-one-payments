<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const produtos =  ref([]);


const apiProdutos = async () => {
  try{
     const response = await axios.get(' http://127.0.0.1:8000/api/produtos');
     const data = response.data;

      if(data.status === 404){
          console.error(data.message);
      }else{
          produtos.value = data.data.map(produtos => ({
            id: produtos.id,
            href: `/produto/${produtos.id}`,
            nome: produtos.nome,
            preco: `R$ ${produtos.preco}`,
            descricao: produtos.descricao,
            imagem: produtos.imagemProduto,
            imagemAlt: `Imagem de ${produtos.nome}`,
          }));
      }

  }catch(error){
    console.log("Erro ao buscar os produtos:", error);
  }
}


onMounted(apiProdutos)

</script>


<template>


  <div class="font-[sans-serif] ">
      <div class="p-4 mx-auto lg:max-w-7xl sm:max-w-full">
        <h2 class="text-4xl font-extrabold text-gray-800 mb-12">Produtos</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 max-xl:gap-4 gap-6">
          <div v-for="produto in produtos" :key="produto.id" class="bg-gray-100 rounded-2xl p-5 cursor-pointer hover:-translate-y-2 transition-all relative">
            <div class="w-5/6 h-[210px] overflow-hidden mx-auto aspect-w-16 aspect-h-8 md:mb-2 mb-4">
              <img :src="produto.imagem" :alt="produto.imagem"
                class="h-full w-full object-contain" />
            </div>
            <div>
              <h3 class="text-lg font-extrabold text-gray-800">
                <RouterLink
                :to="produto.href"
                class="hover:text-blue-600">
                  <span aria-hidden="true" class="absolute inset-0" />
                  {{ produto.nome }}
                </RouterLink></h3>
              <h4 class="text-lg text-gray-800 font-bold mt-4">{{ produto.preco }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>


</template>
