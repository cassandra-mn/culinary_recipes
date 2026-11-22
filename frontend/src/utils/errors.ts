import { isAxiosError } from 'axios';

export function getErrorMessage(error: unknown): string {
    if (!isAxiosError(error)) {
        return 'Erro inesperado. Tente novamente.';
    }

    if (!error.response) return 'Não foi possível conectar ao servidor';

    return error.response.data?.message ?? 'Erro inesperado. Tente novamente.';
}
