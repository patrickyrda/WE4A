import './bootstrap.js';
import './styles/app.css';

import { Application } from '@hotwired/stimulus';
import UeDashboardController from './controllers/ue_dashboard_controller.js';
import PaginationController from './controllers/pagination_controller.js';
import PostController from './controllers/post_controller.js';
import PostFormController from './controllers/post_form_controller.js';
import AdminDashboardController from './controllers/admin_dashboard_controller.js';

window.Stimulus = Application.start();
Stimulus.register('ue-dashboard', UeDashboardController);
Stimulus.register('pagination', PaginationController);
Stimulus.register('post', PostController);
Stimulus.register('post-form', PostFormController);
Stimulus.register('admin-dashboard', AdminDashboardController);