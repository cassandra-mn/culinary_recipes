import api from '@/services/api';

export interface Recipe {
    id: number | null,
    user_id: number,
    category_id: number | null,
    name: string | null,
    preparation_time: number | null,
    preparation_method: string,
    portions: number | null,
    ingredients: string | null,
    created_at: string,
    updated_at: string,
}

export interface Category {
    id: number,
    name: string,
}

export type RecipePayload = Omit<
    Recipe,
    'id' | 'user_id' | 'created_at' | 'updated_at'
>

export async function getCategories() {
    const { data } = await api.get('/categories');

    return data.data as Category[];
}

export async function getRecipes() {
    const { data } = await api.get('/recipes');

    return data.data as Recipe[];
}

export async function getRecipeById(id: any) {
    const { data } = await api.get(`/recipes/${id}`);

    return data.data as Recipe;
}

export async function createRecipe(recipe: RecipePayload) {
    const { data } = await api.post('/recipes', recipe);

    return data.data as Recipe;
}

export async function updateRecipe(id: any, recipe: RecipePayload) {
    const { data } = await api.patch(`/recipes/${id}`, recipe);

    return data.data as Recipe;
}

export async function deleteRecipe(id: any) {
    await api.delete(`/recipes/${id}`);
}
