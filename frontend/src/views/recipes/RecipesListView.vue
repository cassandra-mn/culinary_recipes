<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth.ts';
import * as recipeService from '@/services/recipeService.ts';

const auth = useAuthStore();
const router = useRouter();

const recipes = ref<any[]>([]);
const loading = ref(false);
const error = ref('');

onMounted(() => {
  loading.value = true;

  async function fetchRecipe() {
    try {
      recipes.value = await recipeService.getRecipes();
    } catch (e) {
      error.value = 'Erro ao buscar receitas.';
    } finally {
      loading.value = false;
    }
  }

  fetchRecipe();
});

async function handleDelete(recipeId: number) {
  if (!confirm('Tem certeza que deseja excluir esta receita?')) return;

  try {
    await recipeService.deleteRecipe(recipeId);

    recipes.value = await recipeService.getRecipes();
  } catch (e) {
    error.value = 'Erro ao excluir receitas.';
  }
}

async function handleLogout() {
  await auth.logout();

  router.push('/login');
}
</script>

<template>
  <section class="auth">
    <h1>Receitas</h1>
    <p><RouterLink :to="{ name: 'recipes-create' }">Adicionar receita</RouterLink></p>
    <button @click.prevent="handleLogout">Sair</button>
    <p v-if="loading">Carregando...</p>
    <p v-else-if="error">{{ error }}</p>
    <ul v-else>
      <li v-for="recipe in recipes" :key="recipe.id">
        {{ recipe.name }}
        <RouterLink :to="{ name: 'recipe-detail', params: { id: recipe.id } }" title="Ver">👁️</RouterLink>
        <RouterLink :to="{ name: 'recipe-edit', params: { id: recipe.id } }" title="Editar">✏️</RouterLink>
        <button type="button" @click="handleDelete(recipe.id)" title="Excluir">Excluir</button>
      </li>
    </ul>
  </section>
</template>

<style scoped>

</style>