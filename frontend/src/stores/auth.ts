import { computed, ref } from 'vue';
import { defineStore } from 'pinia';
import * as authService from '@/services/authService';
import type { Credentials, User } from '@/services/authService';

export const useAuthStore = defineStore('auth', () => {
    const token = ref<string | null>(localStorage.getItem('token'));
    const user = ref<User | null>(JSON.parse(localStorage.getItem('user') ?? 'null'));

    const isAuthenticated = computed((): boolean => !!token.value);

    async function login(credentials: Credentials) {
        const data: { token: string, user: User } = await authService.login(credentials);

        token.value = data.token;
        user.value = data.user;

        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));
    }

    async function logout() {
        try {
            await authService.logout();
        } catch (error) {
            //
        } finally {
            token.value = null;
            user.value = null;

            localStorage.removeItem('token');
            localStorage.removeItem('user');
        }
    }

    return { token, user, isAuthenticated, login, logout };
});
