<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { RouterLink, useRoute, useRouter } from 'vue-router';
import { getErrorMessage } from '@/utils/errors.ts';
import * as recipeService from '@/services/recipeService.ts';
import type { RecipePayload } from "@/services/recipeService.ts";

const route = useRoute();
const router = useRouter();

const recipeId = computed(() => route.params.id);
const isEditing = computed(() => Boolean(recipeId.value));

const categories = ref<any[]>([]);
const loading = ref(false);
const error = ref('');

const form = ref<RecipePayload>({
  category_id: null,
  name: '',
  preparation_time: null,
  preparation_method: '',
  portions: null,
  ingredients: '',
});

onMounted(() => {
  loading.value = true;

  async function fetchCategories() {
    try {
      if (isEditing.value && recipeId.value) {
        const data = await recipeService.getRecipeById(recipeId.value);

        if (data) {
          form.value = {
            category_id: data.category_id,
            name: data.name,
            preparation_time: data.preparation_time,
            preparation_method: data.preparation_method,
            portions: data.portions,
            ingredients: data.ingredients,
          };
        }
      }

      categories.value = await recipeService.getCategories();
    } catch (e) {
      error.value = 'Erro ao buscar categorias.';
    } finally {
      loading.value = false;
    }
  }

  fetchCategories();
});

async function handleSubmit() {
  error.value = '';

  if (!form.value.preparation_method) {
    error.value = 'O modo de prepro é obrigatório.'
    return
  }

  loading.value = true;

  try {
    if (isEditing.value) {
      await recipeService.updateRecipe(recipeId.value, form.value);
    } else {
      await recipeService.createRecipe(form.value);
    }

    router.push({ name: 'recipes-list' });
  } catch (e) {
    error.value = getErrorMessage(e);
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <section class="auth">
    <h1>{{ isEditing ? 'Editar Receita' : 'Nova Receita' }}</h1>
    <p v-if="error" class="auth-error" role="alert">{{ error }}</p>
    <form @submit.prevent="handleSubmit">
      <label>
        Nome
        <input v-model="form.name" type="text" maxlength="45" />
      </label>
      <label>
        Tempo de preparo (minutos)
        <input v-model="form.preparation_time" type="number" min="0" />
      </label>
      <label>
        Modo de preparação
        <input v-model="form.preparation_method" type="text" maxlength="100" required />
      </label>
      <label>
        Porções
        <input v-model="form.portions" type="number" min="0" />
      </label>
      <label>
        Ingredientes
        <input v-model="form.ingredients" type="text" maxlength="100" />
      </label>
      <button type="submit" :disabled="loading">
        {{ isEditing ? 'Salvar Alterações' : 'Adicionar Receita' }}
      </button>
    </form>
    <p><RouterLink :to="{ name: 'recipes-list' }">Voltar</RouterLink></p>
  </section>
</template>

<style scoped>

</style>