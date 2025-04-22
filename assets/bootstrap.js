// assets/bootstrap.js

// on importe directement Stimulus depuis le dossier vendor
import { Application } from './vendor/@hotwired/stimulus/stimulus.index.js';

// puis on importe notre contrôleur
import UeDashboardController from './controllers/ue_dashboard_controller.js';

// et on enregistre tout manuellement
const application = Application.start();
application.register('ue-dashboard', UeDashboardController);

console.log('Stimulus + UE Dashboard branché');
