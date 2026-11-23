<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth.ts';
import * as recipeService from '@/services/recipeService.ts';
import { EyeIcon, TrashIcon, PencilIcon } from '@heroicons/vue/24/outline';

const auth = useAuthStore();
const router = useRouter();

const recipes = ref<any[]>([]);
const loading = ref(false);
const error = ref('');
const search = ref('');

const filteredRecipes = computed(() => {
  const term = search.value.trim().toLowerCase();

  if (!term) return recipes.value;

  return recipes.value.filter((recipe) =>
      recipe.name?.toLowerCase().includes(term)
  );
});

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
    <div class="d-flex">
      <h1>Receitas</h1>
      <button @click.prevent="handleLogout">Sair</button>
    </div>
    <button type="button">
      <RouterLink :to="{ name: 'recipes-create' }">
        <p class="text-fff m-0">Adicionar receita</p>
      </RouterLink>
    </button>
    <input v-model="search" type="search" placeholder="Buscar receita pelo nome" />
    <p v-if="loading">Carregando...</p>
    <p v-else-if="error">{{ error }}</p>
    <ul v-else class="text-fff px-1">
      <li v-for="recipe in filteredRecipes" :key="recipe.id" class="d-flex">
        <div class="d-flex">{{ recipe.name ?? '-' }}</div>
        <div class="d-flex">
          <RouterLink :to="{ name: 'recipe-detail', params: { id: recipe.id } }" title="Ver">
            <button type="button" class="action">
              <EyeIcon class="size-25 text-blue-500 icon" />
            </button>
          </RouterLink>
          <RouterLink :to="{ name: 'recipe-edit', params: { id: recipe.id } }" title="Editar">
            <button type="button" class="action">
              <PencilIcon class="size-25 text-yellow-500 icon" />
            </button>
          </RouterLink>
          <button type="button" class="action" @click="handleDelete(recipe.id)" title="Excluir">
            <TrashIcon class="size-25 text-red-500 icon" />
          </button>
        </div>
      </li>
    </ul>
    <p v-if="!loading && !error && !filteredRecipes.length">Nenhuma receita encontrada.</p>
  </section>
</template>

<style scoped>

</style>