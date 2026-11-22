import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth.ts';

const router= createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            redirect: { name: 'recipes' },
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('@/views/LoginView.vue'),
            meta: { guest: true },
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('@/views/RegisterView.vue'),
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
            name: 'recipes',
            component: () => import('@/views/RecipesView.vue'),
            meta: { requiresAuth: true },
        }
    ],
});

router.beforeEach((to) => {
    const auth = useAuthStore();

    if (to.meta.requiresAuth && !auth.isAuthenticated) return { name: 'login' };
    if (to.meta.guest && auth.isAuthenticated) return { name: 'recipes' };
});

export default router;
