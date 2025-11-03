export function fFormatDate(dateStr) {
    if (!dateStr) return "";

    if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
        return dateStr;
    }

    const [dia, mes, ano] = dateStr.split("/");
    return `${ano}-${mes.padStart(2, "0")}-${dia.padStart(2, "0")}`;
}

export function fGetCategoriaId(categoriaAtual, categories) {
    const categoriaAchada = categories.find((c) => c.name === categoriaAtual);
    return categoriaAchada.id || "Não definido";
}

// Função para mostrar o toast
export function fShowToast(
    message,
    toastMessage,
    toastType,
    toastVisible,
    showToast,
    type = "success",
    duration = 3000,
    ies_model = true,
    fFunctionAction
) {
    toastMessage.value = message;
    toastType.value = type;
    toastVisible.value = true;
    showToast.value = true;

    return new Promise((resolve) => {
        setTimeout(() => {
            toastVisible.value = false;

            if (ies_model && type === "success") {
                if (Array.isArray(fFunctionAction)) {
                    fFunctionAction.forEach(fn => typeof fn === "function" && fn());
                } else if (typeof fFunctionAction === "function") {
                    fFunctionAction();
                }
            }

            resolve();
            showToast.value = false;
        }, duration);
    });
}


