import { ref } from 'vue';

interface Toast {
    type: 'success' | 'error' | 'info' | 'warning';
    message: string;
}

const toast = ref<Toast | null>(null);

export function useToast() {
    const showToast = (type: Toast['type'], message: string) => {
        toast.value = { type, message };
        setTimeout(() => {
            toast.value = null;
        }, 3000);
    };

    return {
        toast,
        showToast
    };
} 