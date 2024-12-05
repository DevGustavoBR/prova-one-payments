<script setup>
 import { useRoute } from 'vue-router';
 import PagamentoCartaoCreditoAxios from '@/components/api/checkout/PagamentoCartaoCreditoAxios.vue';

 const route = useRoute();
 const transacaoId = route.params.id;

PagamentoCartaoCreditoAxios
</script>


<template>
 <PagamentoCartaoCreditoAxios :transacaoId="transacaoId" />
</template>
