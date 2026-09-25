import './bootstrap';

import Alpine from 'alpinejs';
import { Chart } from 'chart.js/auto';

// Exposto para os gráficos inicializados nos dashboards. O bundle é
// carregado como módulo (deferred), por isso os scripts dos dashboards
// só o consomem dentro de DOMContentLoaded.
window.Chart = Chart;

window.Alpine = Alpine;

Alpine.start();
