import { Controller } from 'https://cdn.jsdelivr.net/npm/@hotwired/stimulus@3.2.2/dist/stimulus.js';
import { Modal } from 'bootstrap';

export default class extends Controller {
  static targets = ['modal', 'modalContent'];

  connect() {
    this.loadUserTable();
  }

  loadUserTable() {
    fetch('/user/admin', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(json => {
      if (json.success) {
        document.getElementById('user-table-container').innerHTML = json.html;
        this.bindUserLinks();
      }
    })
    .catch(e => console.error('Erreur AJAX loadUserTable', e));
  }

  bindUserLinks() {
    document.querySelectorAll('a[data-action="user-admin#openEditModal"]').forEach(link => {
      link.addEventListener('click', e => this.openEditModal(e));
    });

    document.querySelectorAll('form[data-action="user-admin#askDelete"]').forEach(form => {
      form.addEventListener('submit', e => this.askDelete(e));
    });
  }

  openEditModal(event) {
    event.preventDefault();
    const url = event.currentTarget.getAttribute('href');

    fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
      this.ensureModalExists();
      this.modalContentTarget.innerHTML = data.form || data.content;
      const modal = new Modal(this.modalTarget);
      modal.show();
      this.bindFormSubmit(modal);
    })
    .catch(e => console.error('Erreur AJAX openEditModal', e));
  }

  bindFormSubmit(modal) {
    const form = this.modalContentTarget.querySelector('form');
    if (!form) return;

    form.addEventListener('submit', e => {
      e.preventDefault();
      const formData = new FormData(form);

      fetch(form.action, {
        method: form.method,
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          modal.hide();
          this.loadUserTable();
        } else if (data.form) {
          this.modalContentTarget.innerHTML = data.form;
          this.bindFormSubmit(modal);
        }
      })
      .catch(e => console.error('Erreur AJAX submit', e));
    });
  }

  askDelete(event) {
    event.preventDefault();
    if (!confirm('Es-tu sûr de vouloir supprimer cet utilisateur ?')) return;

    const form = event.target;
    const formData = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        this.loadUserTable();
      } else {
        alert(data.message || 'Erreur inconnue');
      }
    })
    .catch(e => console.error('Erreur AJAX suppression', e));
  }

  ensureModalExists() {
    if (!this.hasModalTarget) {
      const modal = document.createElement('div');
      modal.className = 'modal fade';
      modal.id = 'modalUserAdmin';
      modal.tabIndex = -1;
      modal.innerHTML = `
        <div class="modal-dialog">
          <div class="modal-content" data-user-admin-target="modalContent"></div>
        </div>
      `;
      document.body.appendChild(modal);
      this.modalTarget = modal;
      this.modalContentTarget = modal.querySelector('[data-user-admin-target="modalContent"]');
    }
  }
}
