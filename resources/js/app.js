import ApexCharts from 'apexcharts';

// Expose ApexCharts so Blade views can instantiate dashboard charts
// (Alpine is provided by Livewire; charts are initialised via x-init).
window.ApexCharts = ApexCharts;
