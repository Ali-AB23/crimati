import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            
        }),
    ],
    //  server: {
    //     host: '0.0.0.0',          // écoute sur toutes les interfaces (IPv4 et IPv6)
    //     port: process.env.VITE_PORT || 5173,
    //     strictPort: true,
    //     origin: 'http://localhost:5173', // force l'URL générée à utiliser localhost
    // },
    server: {
        host: '0.0.0.0',
        port: process.env.VITE_PORT || 5173,
        strictPort: true,
        origin: 'http://localhost:5173', // pour générer les URLs
        cors: {
            origin: ['http://127.0.0.1:8000', 'http://localhost:8000'], // origines autorisées
            credentials: true,
        },
    },
    
   
});
