import { Controller } from "@hotwired/stimulus";
import { Modal } from "bootstrap";

export default class extends Controller {
  static targets = ['list'];
  static values  = { postId: Number, csrf: String };
  connect() {
    this.setupLinks();
    this.setupDeleteForms();
  }

  setupLinks() {
    document.querySelectorAll('a[href]').forEach(link => {
      if (link.href.includes('/post/new')) {
        link.addEventListener('click', (e) => this.handleNewClick(e));
      } else if (link.href.match(/\/post\/\d+\/edit/)) {
        link.addEventListener('click', (e) => this.handleEditClick(e));
      }
    });
  }

  setupDeleteForms() {
    document.querySelectorAll('form[action]').forEach(form => {
      if (form.action.match(/\/post\/\d+$/)) {
        form.addEventListener('submit', (e) => this.handleDeleteSubmit(e));
      }
    });
  }

  handleNewClick(event) {
    event.preventDefault();
    const url = event.currentTarget.href;
    this.fetchFormAndShowModal(url);
  }

  handleEditClick(event) {
    event.preventDefault();
    const url = event.currentTarget.href;
    this.fetchFormAndShowModal(url);
  }

  handleDeleteSubmit(event) {
    event.preventDefault();
    if (!confirm('Es-tu sûr de vouloir supprimer ce post ?')) {
      return;
    }
    const form = event.target;
    const formData = new FormData(form);

    fetch(form.action, {
      method: "POST",
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        window.location.reload();
      } else {
        alert('Erreur lors de la suppression');
      }
    })
    .catch(error => {
      console.error("Erreur AJAX :", error);
      alert("Erreur réseau");
    });
  }

  fetchFormAndShowModal(url) {
    fetch(url)
      .then(response => response.json())
      .then(data => {
        this.ensureModalExists();
        this.modalContent.innerHTML = data.form;
        const modalInstance = new Modal(this.modal);
        modalInstance.show();
        this.bindFormSubmit(modalInstance);
      })
      .catch(error => {
        console.error("Erreur AJAX :", error);
        alert("Erreur réseau");
      });
  }

  ensureModalExists() {
    this.modal = document.getElementById('modalPost');
    if (!this.modal) {
      this.modal = document.createElement('div');
      this.modal.id = 'modalPost';
      this.modal.classList.add('modal', 'fade');
      this.modal.tabIndex = -1;
      this.modal.innerHTML = `
        <div class="modal-dialog">
          <div class="modal-content" id="modalPostContent"></div>
        </div>
      `;
      document.body.appendChild(this.modal);
    }
    this.modalContent = document.getElementById('modalPostContent');
  }

  bindFormSubmit(modalInstance) {
    const form = this.modalContent.querySelector('form');
    if (!form) return;

    form.addEventListener('submit', (event) => {
      event.preventDefault();
      const formData = new FormData(form);

      fetch(form.action, {
        method: form.method,
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          modalInstance.hide();
          window.location.reload();
        } else if (data.form) {
          this.modalContent.innerHTML = data.form;
          this.bindFormSubmit(modalInstance);
        }
      })
      .catch(error => {
        console.error("Erreur AJAX :", error);
        alert("Erreur réseau");
      });
    });
  }
  askDelete(event) {
    const id   = event.currentTarget.dataset.postIdValue;
    const csrf = event.currentTarget.dataset.csrfValue;

    if (!confirm('Confirmer la suppression de ce post ?')) return;
    const body = new FormData();
    body.append('_token', csrf);
    fetch(`/post/${id}`, {
      method : 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body
    })
      .then(r => r.json())
      .then(json => {
        if (json.success) {
          document.getElementById(`post-${id}`).remove();
        } else {
          alert(json.message || 'Erreur inconnue');
        }
      })
      .catch(() => alert('Erreur'));
  }
}