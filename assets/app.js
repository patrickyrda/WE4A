import './bootstrap.js';
import './styles/app.css';

import { Application } from '@hotwired/stimulus';
import UeDashboardController from './controllers/ue_dashboard_controller.js';

window.Stimulus = Application.start();
Stimulus.register('ue-dashboard', UeDashboardController);

console.log('Stimulus chargé et ue-dashboard branché');
