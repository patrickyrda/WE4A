import { Controller } from "@hotwired/stimulus";
import { Modal } from "bootstrap";

export default class extends Controller {
  static targets = ["modal", "modalContent", "etudiantSelect"];

  connect() {
    console.log('Stimulus UE Admin fonctionne');
    this.currentUeId = null;
    this.bindUeLinks();
  }

  // Partie "Ajouter Étudiant"
  ouvrirModal(event) {
    console.log('Bouton Ajouter Étudiant cliqué');
    this.currentUeId = event.currentTarget.dataset.ueId;
    const modalElement = this.modalTarget;
    const modalInstance = new Modal(modalElement);
    modalInstance.show();
  }

  ajouterEtudiant() {
    const etudiantId = this.etudiantSelectTarget.value;

    fetch("/user/api/ajouter-etudiant", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        ue_id: this.currentUeId,
        etudiant_id: etudiantId
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        window.location.reload();
      } else {
        alert("Erreur : " + data.message);
      }
    })
    .catch(error => {
      console.error("Erreur AJAX :", error);
      alert("Erreur réseau");
    });
  }

  // Partie "Voir / Modifier / Créer UE via modals"
  bindUeLinks() {
    document.querySelectorAll('a[href]').forEach(link => {
      if (link.href.includes('/ue/admin/') && link.href.includes('/edit')) {
        link.addEventListener('click', (e) => this.openEditModal(e));
      } else if (link.href.includes('/ue/admin/') && !link.href.includes('/edit') && !link.href.includes('/new')) {
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
    fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
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
    .catch(err => console.error('Erreur AJAX:', err));
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
        .catch(err => console.error('Erreur AJAX submit:', err));
    });
  }

  ensureModalExists() {
    if (!this.hasModalTarget) {
      const modal = document.createElement('div');
      modal.classList.add('modal', 'fade');
      modal.id = 'modalUe';
      modal.tabIndex = -1;
      modal.innerHTML = `
        <div class="modal-dialog">
          <div class="modal-content" data-ue-dashboard-target="modalContent"></div>
        </div>
      `;
      document.body.appendChild(modal);
      this.modalTarget = modal;
      this.modalContentTarget = modal.querySelector('[data-ue-dashboard-target="modalContent"]');
    }
  }
}
