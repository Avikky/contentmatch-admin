import { ref } from 'vue';

const toasts = ref([]);
let toastId = 0;

export function useToast() {
  const show = (message, type = 'info', duration = 5000) => {
    const id = toastId++;
    toasts.value.push({
      id,
      message,
      type,
      duration,
    });

    return id;
  };

  const success = (message, duration = 5000) => {
    return show(message, 'success', duration);
  };

  const error = (message, duration = 5000) => {
    return show(message, 'error', duration);
  };

  const warning = (message, duration = 5000) => {
    return show(message, 'warning', duration);
  };

  const info = (message, duration = 5000) => {
    return show(message, 'info', duration);
  };

  const remove = (id) => {
    const index = toasts.value.findIndex((toast) => toast.id === id);
    if (index > -1) {
      toasts.value.splice(index, 1);
    }
  };

  return {
    toasts,
    show,
    success,
    error,
    warning,
    info,
    remove,
  };
}
