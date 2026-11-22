<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth.ts';

const auth = useAuthStore();
const router = useRouter();

async function handleLogout() {
  await auth.logout();

  router.push('/login');
}
</script>

<template>
  <section class="auth">
    <h1>Receitas</h1>
    <button @click.prevent="handleLogout">Sair</button>
  </section>
</template>

<style scoped>

</style>