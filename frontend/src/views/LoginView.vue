<script setup lang="ts">
import { ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth.ts';
import { getErrorMessage } from '@/utils/errors.ts';

const auth = useAuthStore();
const router = useRouter();

const login = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);

async function handleSubmit() {
  error.value = '';
  loading.value = true;

  try {
    let credentials = {
      login: login.value.trim(),
      password: password.value,
    };

    await auth.login(credentials);

    router.push({ name: 'recipes' });
  } catch (e) {
    error.value = getErrorMessage(e);
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <section class="auth">
    <h1>Entrar</h1>
    <p v-if="error" class="auth-error" role="alert">{{ error }}</p>
    <form @submit.prevent="handleSubmit">
      <label>
        Login
        <input v-model="login" type="text" maxlength="100" required autocomplete="username" />
      </label>
      <label>
        Senha
        <input v-model="password" type="password" maxlength="72" required autocomplete="password" />
      </label>
      <button type="submit" :disabled="loading">
        {{ loading ? 'Entrando...' : 'Entrar' }}
      </button>
    </form>
    <p>Não tem conta? <RouterLink :to="{ name: 'register' }">Cadastre-se</RouterLink></p>
  </section>
</template>

<style scoped>

</style>