import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth.ts';

const router= createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            redirect: { name: 'recipes-list' },
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('@/views/auth/LoginView.vue'),
            meta: { guest: true },
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('@/views/auth/RegisterView.vue'),
            meta: { guest: true },
        },
        {
            path: '/logout',
            name: 'logout',
            redirect: { name: 'login' },
            meta: { requiresAuth: true },
        },
        {
            path: '/recipes',
            name: 'recipes-list',
            component: () => import('@/views/recipes/RecipesListView.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/recipes/create',
            name: 'recipes-create',
            component: () => import('@/views/recipes/RecipeFormView.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/recipes/:id',
            name: 'recipe-detail',
            component: () => import('@/views/recipes/RecipeDetailView.vue'),
            meta: { requiresAuth: true }
        },
        {
            path: '/recipes/:id/edit',
            name: 'recipe-edit',
            component: () => import('@/views/recipes/RecipeFormView.vue'),
            meta: { requiresAuth: true },
        }
    ],
});

router.beforeEach((to) => {
    const auth = useAuthStore();

    if (to.meta.requiresAuth && !auth.isAuthenticated) return { name: 'login' };
    if (to.meta.guest && auth.isAuthenticated) return { name: 'recipes-list' };
});

export default router;
