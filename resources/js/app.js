import '@fontsource/inter';
import '@phosphor-icons/web/regular/style.css';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';
import { apiFetch } from './api.js';

// Ekspos ke window object agar bisa digunakan di inline script Blade
window.apiFetch = apiFetch;
window.Swal = Swal;
window.Chart = Chart;
