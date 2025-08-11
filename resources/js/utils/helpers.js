export function fFormatDate(dateStr) {
    if (!dateStr) return '';

    if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
        return dateStr;
    }

    const [dia, mes, ano] = dateStr.split('/');
    return `${ano}-${mes.padStart(2, '0')}-${dia.padStart(2, '0')}`;
}

export function fGetCategoriaId(categoriaAtual, categories) {
    const categoriaAchada = categories.find(c => c.name === categoriaAtual);
    return categoriaAchada.id || 'Não definido';
}
