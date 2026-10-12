import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';
import { http, toast, showErrors, clearErrors, ValidationError } from './ajax.js';

// Expose ApexCharts so Blade views can instantiate dashboard charts
// (initialised via Alpine x-init in the dashboard views).
window.ApexCharts = ApexCharts;

// AJAX helpers used by the per-page inline scripts (admin CRUD, application
// form and workflow actions). Exposed on window so page scripts can run
// without their own Vite entry point.
window.besqala = { http, toast, showErrors, clearErrors, ValidationError };

// Alpine powers all client-side interactivity (sidebar, dropdowns, dark-mode,
// tabs, modals). It used to be provided by Livewire; now it is bundled directly.
window.Alpine = Alpine;
Alpine.start();
