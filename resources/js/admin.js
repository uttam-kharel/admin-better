// Admin-only bundle: Chart.js analytics are only used on the /admin/analytics page.
// Loaded by the admin layout so public pages never download ~200KB of charting code.
import './charts';
