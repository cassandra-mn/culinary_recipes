<script setup lang="ts">
import { ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth.ts';
import { getErrorMessage } from '@/utils/errors.ts';
import * as authService from '@/services/authService.ts';

const auth = useAuthStore();
const router = useRouter();

const name = ref('');
const login = ref('');
const password = ref('');
const confirmPassword = ref('');
const error = ref('');
const loading = ref(false);

async function handleSubmit() {
  error.value = '';

  if (password.value !== confirmPassword.value) {
    error.value = 'As senhas não conferem.'
    return
  }

  loading.value = true;

  try {
    let credentials = {
      login: login.value.trim(),
      password: password.value,
    };
    let payload = {
      name: name.value.trim() || undefined,
      ...credentials,
    };

    await authService.register(payload);
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
    <h1>Cadastrar</h1>
    <p v-if="error" class="auth-error" role="alert">{{ error }}</p>
    <form @submit.prevent="handleSubmit">
      <label>
        Nome
        <input v-model="name" type="text" maxlength="100" autocomplete="username" />
      </label>
      <label>
        Login
        <input v-model="login" type="text" maxlength="100" required autocomplete="username" />
      </label>
      <label>
        Senha
        <input v-model="password" type="password" maxlength="72" required autocomplete="password" />
      </label>
      <label>
        Confirmar senha
        <input v-model="confirmPassword" type="password" maxlength="72" required autocomplete="password" />
      </label>
      <button type="submit" :disabled="loading">
        {{ loading ? 'Cadastrando...' : 'Cadastrar' }}
      </button>
    </form>
    <p>Já tem conta? <RouterLink :to="{ name: 'login' }">Entrar</RouterLink></p>
  </section>
</template>

<style scoped>

</style>