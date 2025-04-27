import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

export default class extends Controller {
  static targets = ['ueContainer', 'userContainer', 'modal', 'modalContent'];

  connect() {
    this.loadUeTable()
  }

  loadUeTable() {
    fetch('/ue/admin', { headers: {'X-Requested-With':'XMLHttpRequest'} })
      .then(r => r.json())
      .then(json => {
        if (json.success) {
          document.getElementById('ue-table-container').innerHTML = json.html
        }
      })
      .catch(e => console.error('Erreur AJAX', e))
  }

  loadUserTable(event) {
    event.preventDefault();
    this._fetchAndInject('/user/admin', 'userContainer');
  }

  _fetchAndInject(url, targetName) {
    if (targetName === 'ueContainer') {
      this.userContainerTarget.innerHTML = '';
    } else {
      this.ueContainerTarget.innerHTML = '';
    }

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => {
        if (!r.ok) throw new Error(`Status ${r.status}`);
        return r.json();
      })
      .then(json => {
        if (json.success) {
          this[targetName + 'Target'].innerHTML = json.html;
          this.bindUserAdminLinks(); // Important si c'est users
        }
      })
      .catch(err => console.error('Erreur AJAX', err));
  }

  bindUserAdminLinks() {
    document.querySelectorAll('a[href]').forEach(link => {
      if (link.href.includes('/user/admin/') && link.href.includes('/edit')) {
        link.addEventListener('click', (e) => this.openEditModal(e));
      } else if (link.href.includes('/user/admin/') && !link.href.includes('/edit') && !link.href.includes('/new')) {
        link.addEventListener('click', (e) => this.openShowModal(e));
      }
    });
  }

  openShowModal(event) {
    event.preventDefault();
    const url = event.currentTarget.href;
    this.fetchModalContent(url, 'content');
  }

  openEditModal(event) {
    event.preventDefault();
    const url = event.currentTarget.href;
    this.fetchModalContent(url, 'form');
  }

  fetchModalContent(url, key) {
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(json => {
        if (json[key]) {
          this.ensureModalExists();
          this.modalContentTarget.innerHTML = json[key];
          const modalInstance = new Modal(this.modalTarget);
          modalInstance.show();

          if (key === 'form') {
            this.bindFormSubmit(modalInstance);
          }
        }
      })
      .catch(err => console.error('Erreur AJAX', err));
  }

  bindFormSubmit(modalInstance) {
    const form = this.modalContentTarget.querySelector('form');
    if (!form) return;

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const formData = new FormData(form);

      fetch(form.action, { method: form.method, body: formData })
        .then(r => r.json())
        .then(json => {
          if (json.success) {
            modalInstance.hide();
            window.location.reload();
          } else if (json.form) {
            this.modalContentTarget.innerHTML = json.form;
            this.bindFormSubmit(modalInstance);
          }
        })
        .catch(err => console.error('Erreur AJAX', err));
    });
  }

  ensureModalExists() {
    if (!this.hasModalTarget) {
      const modal = document.createElement('div');
      modal.classList.add('modal', 'fade');
      modal.id = 'modalAdmin';
      modal.tabIndex = -1;
      modal.innerHTML = `
        <div class="modal-dialog">
          <div class="modal-content" data-admin-dashboard-target="modalContent"></div>
        </div>
      `;
      document.body.appendChild(modal);
      this.modalTarget = modal;
      this.modalContentTarget = modal.querySelector('[data-admin-dashboard-target="modalContent"]');
    }
  }
}
