<script setup>
 import { useRoute } from 'vue-router';
 import TranscaoDetalhesAxios from '@/components/api/TranscaoDetalhesAxios.vue';

 const route = useRoute();
 const transacaoId = route.params.id;

</script>

<template>
   <TranscaoDetalhesAxios :transacaoId="transacaoId"/>
</template>
