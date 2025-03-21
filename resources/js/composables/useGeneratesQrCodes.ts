import axios from 'axios';

export function useGeneratesQrCodes() {
    const generateQrCode = async (url: string, path: string): Promise<string> => {
        try {
            const response = await axios.post('/api/qrcodes/generate', {
                url,
                path
            });
            return response.data.url;
        } catch (error) {
            console.error('Erreur lors de la génération du QR code:', error);
            throw error;
        }
    };

    return {
        generateQrCode
    };
} 