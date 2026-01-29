import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Only initialize Echo if Pusher credentials are configured
if (import.meta.env.VITE_PUSHER_APP_KEY && import.meta.env.VITE_PUSHER_APP_KEY !== 'your-pusher-app-key') {
window.Echo = new Echo({
    broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
    forceTLS: true,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
        enableLogging: import.meta.env.DEV || false,
});
} else {
    console.warn('Pusher credentials not configured. Real-time features will be disabled.');
    window.Echo = null;
}
