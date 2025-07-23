export const useCepService = () => {
  const buscarEnderecoPorCep = async (cep) => {
    try {
      const cepNumerico = cep.replace(/\D/g, '');

      if (cepNumerico.length !== 8) {
        throw new Error('CEP deve conter 8 dígitos');
      }

      const response = await fetch(`https://brasilapi.com.br/api/cep/v2/${cepNumerico}`);

      if (!response.ok) {
        throw new Error('CEP não encontrado');
      }

      const data = await response.json();

      return {
        street: data.street || '',
        district: data.neighborhood || '',
        city: data.city || '',
        state: data.state || ''
      };

    } catch (error) {
      console.error('Erro ao buscar CEP:', error);
      throw error; // Rejeita a promise para tratamento no componente
    }
  };

  return { buscarEnderecoPorCep };
};
