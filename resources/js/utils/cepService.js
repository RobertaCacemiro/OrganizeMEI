export const useCepService = () => {
    const buscarEnderecoPorCep = async (cep) => {
        try {
            const cepNumerico = cep.replace(/\D/g, "");

            if (cepNumerico.length !== 8) {
                throw new Error("CEP deve conter 8 dígitos.");
            }

            const response = await fetch(
                `https://brasilapi.com.br/api/cep/v2/${cepNumerico}`
            );

            if (!response.ok) {
                if (response.status === 404) {
                    throw new Error("CEP não encontrado.");
                } else {
                    throw new Error(
                        "Erro ao consultar o CEP. Tente novamente mais tarde."
                    );
                }
            }

            const data = await response.json();

            return {
                street: data.street || "",
                district: data.neighborhood || "",
                city: data.city || "",
                state: data.state || "",
                complement: data.complement || "",
            };
        } catch (error) {
            if (error.message.includes("fetch")) {
                throw new Error(
                    "Erro ao buscar endereço pelo CEP. Faço o cadastro de forma manual, por favor"
                );
            }
            console.error("Erro ao buscar CEP:", error);
            throw error;
        }
    };

    return { buscarEnderecoPorCep };
};
