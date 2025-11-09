// Função única para CPF/CNPJ
export function fValidaCpfCnpj(valor) {
    const num = valor.replace(/[^\d]/g, "");

    if (num.length === 11) {
        if (/^(\d)\1+$/.test(num)) return false;

        let soma = 0;
        for (let i = 0; i < 9; i++) soma += parseInt(num.charAt(i)) * (10 - i);
        let resto = 11 - (soma % 11);
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(num.charAt(9))) return false;

        soma = 0;
        for (let i = 0; i < 10; i++) soma += parseInt(num.charAt(i)) * (11 - i);
        resto = 11 - (soma % 11);
        if (resto === 10 || resto === 11) resto = 0;

        return resto === parseInt(num.charAt(10));
    }

    if (num.length === 14) {
        if (/^(\d)\1+$/.test(num)) return false;

        let tamanho = num.length - 2;
        let numeros = num.substring(0, tamanho);
        let digitos = num.substring(tamanho);
        let soma = 0;
        let pos = tamanho - 7;

        for (let i = tamanho; i >= 1; i--) {
            soma += parseInt(numeros.charAt(tamanho - i)) * pos--;
            if (pos < 2) pos = 9;
        }

        let resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
        if (resultado !== parseInt(digitos.charAt(0))) return false;

        tamanho++;
        numeros = num.substring(0, tamanho);
        soma = 0;
        pos = tamanho - 7;

        for (let i = tamanho; i >= 1; i--) {
            soma += parseInt(numeros.charAt(tamanho - i)) * pos--;
            if (pos < 2) pos = 9;
        }

        resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
        return resultado === parseInt(digitos.charAt(1));
    }

    return false;
}

export function fValidaCnpj(cnpj) {
    if (!cnpj) return false;

    cnpj = cnpj.replace(/[^\d]+/g, "");

    if (cnpj.length !== 14) return false;

    // Rejeita sequência de números iguais (ex: 11111111111111)
    if (/^(\d)\1+$/.test(cnpj)) return false;

    const validarDigito = (base) => {
        let soma = 0;
        let pos = base.length - 7;
        for (let i = 0; i < base.length; i++) {
            soma += base[i] * pos--;
            if (pos < 2) pos = 9;
        }
        return soma % 11 < 2 ? 0 : 11 - (soma % 11);
    };

    const numeros = cnpj.substring(0, 12);
    const digitos = cnpj.substring(12);

    const digito1 = validarDigito(numeros);
    const digito2 = validarDigito(numeros + digito1);

    return digitos === `${digito1}${digito2}`;
}
