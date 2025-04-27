// assets/controllers/admin_dashboard_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['container'];

  connect() {
    this.loadUeTable();
  }

  loadUeTable() {
    fetch('/ue/admin', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(r => {
        if (!r.ok) throw new Error(`Status ${r.status}`);
        return r.json();
      })
      .then(json => {
        if (json.success) {
          this.containerTarget.innerHTML = json.html;
        }
      })
      .catch(e => console.error('Erreur AJAX', e));
  }
}