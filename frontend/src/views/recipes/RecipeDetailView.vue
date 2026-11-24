<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import * as recipeService from '@/services/recipeService.ts';

const route = useRoute();

const recipe = ref<any>([]);
const category = ref('');
const loading = ref(false);
const error = ref('');

const recipeId = computed(() => route.params.id);

onMounted(() => {
  loading.value = true;

  async function getRecipe() {
    try {
      recipe.value = await recipeService.getRecipeById(recipeId.value);

      const categories = await recipeService.getCategories();
      category.value = categories.find(c => c.id === recipe.value.category_id)?.name ?? '-';
    } catch (e) {
      error.value = 'Erro ao buscar receita.';
    } finally {
      loading.value = false;
    }
  }

  getRecipe();
});
</script>

<template>
  <section class="auth">
    <h1>{{ recipe.name }}</h1>
    <p v-if="error" class="auth-error" role="alert">{{ error }}</p>
    <form>
      <label>
        Nome
        <input v-model="recipe.name" type="text" maxlength="45" readonly />
      </label>
      <label>
        Categoria
        <input v-model="category" type="text" maxlength="45" readonly />
      </label>
      <label>
        Tempo de preparo (minutos)
        <input v-model="recipe.preparation_time" type="number" min="0" readonly />
      </label>
      <label>
        Modo de preparação
        <input v-model="recipe.preparation_method" type="text" maxlength="100" readonly />
      </label>
      <label>
        Porções
        <input v-model="recipe.portions" type="number" min="0" readonly />
      </label>
      <label>
        Ingredientes
        <input v-model="recipe.ingredients" type="text" maxlength="100" readonly />
      </label>
    </form>
    <p><RouterLink :to="{ name: 'recipes-list' }">Voltar</RouterLink></p>
  </section>
</template>

<style scoped>

</style>