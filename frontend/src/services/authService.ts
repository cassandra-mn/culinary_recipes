import api from '@/services/api';

export interface User {
    id: number,
    name: string | null,
    login: string,
    created_at: string,
    updated_at: string,
}

export interface Credentials {
    login: string,
    password: string,
}

export interface RegisterData extends Credentials {
    name?: string,
}

export async function login(credentials: Credentials) {
    const { data } = await api.post('/login', credentials);

    return data.data as { token: string, user: User };
}

export async function register(payload: RegisterData) {
    const { data } = await api.post('/register', payload);

    return data.data as number;
}

export async function logout() {
    await api.post('/logout');
}
