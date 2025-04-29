import { Controller } from 'https://cdn.jsdelivr.net/npm/@hotwired/stimulus@3.2.2/dist/stimulus.js';
import { Modal } from 'bootstrap';

export default class extends Controller {
  openCreateForm(event) {
    event.preventDefault();
    this.fetchForm('/ue/new');
  }

  openEditForm(event) {
    event.preventDefault();
    const url = event.currentTarget.getAttribute('data-url');
    this.fetchForm(url);
  }

  fetchForm(url) {
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(response => response.json())
      .then(data => {
        document.getElementById('modalUeContent').innerHTML = data.form;
        const modal = new Modal(document.getElementById('modalUe'));
        modal.show();
        this.bindFormSubmit(modal);
      })
      .catch(error => console.error('Erreur AJAX :', error));
  }

  bindFormSubmit(modal) {
    const form = document.querySelector('#modalUeContent form');
    if (!form) return;

    form.addEventListener('submit', (event) => {
      event.preventDefault();
      const formData = new FormData(form);

      fetch(form.action, {
        method: form.method,
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          modal.hide();
          document.querySelector('[data-controller="admin-dashboard"]').controller.loadUeTable(); // Recharge la liste
        } else if (data.form) {
          document.getElementById('modalUeContent').innerHTML = data.form;
          this.bindFormSubmit(modal);
        }
      })
      .catch(error => console.error('Erreur AJAX submit form :', error));
    });
  }
}
