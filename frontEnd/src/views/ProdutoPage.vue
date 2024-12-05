<script setup>
import ProdutoAxios from '../components/api/ProdutoAxios.vue';
import { useRoute } from 'vue-router';

const route = useRoute();

console.log(route.params.nome);
console.log(route.params.id);

const produtoId = route.params.id

</script>

<template>
  <ProdutoAxios :produtoId="produtoId"/>
</template>


