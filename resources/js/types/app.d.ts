import '@inertiajs/core';

declare module '@inertiajs/core' {
    interface PageProps {
        auth: {
            user: {
                id: number;
                name: string;
                email: string;
                roles: Array<{
                    id: number;
                    name: string;
                }>;
            };
        };
    }
}

export interface Stock {
    id: number;
    estimated_quantity: number;
    local_alert_threshold: number;
    supply: {
        id: number;
        name: string;
        reference: string;
        packaging: string;
    };
    location: {
        id: number;
        name: string;
        site: {
            id: number;
            name: string;
        };
    };
    alertes?: Array<{
        id: number;
        type: 'rupture' | 'seuil_atteint';
        message: string;
        created_at: string;
    }>;
} 