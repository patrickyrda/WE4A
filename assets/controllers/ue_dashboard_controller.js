import { Controller } from "@hotwired/stimulus";
import { Modal } from "bootstrap";

export default class extends Controller {
  // Definition des cibles (modal, son contenu et l'etudiant)
  static targets = ["modal", "modalContent", "etudiantSelect"];

  connect() {
    // Verification sur la console que Stimulus fonctionne
    console.log('Stimulus UE Admin fonctionne');
    // Initialise l'id de l'ue courrante.
    this.currentUeId = null;
    // Permet de lier les liens pour voir et modifier les ue.
    this.bindUeLinks();
  }

  // Partie "Ajouter Étudiant"
  ouvrirModal(event) {
    // Log sur la console pour debug
    console.log('Bouton Ajouter Étudiant cliqué');
    // On recupere l'id de l'ue grace à l'attribue
    this.currentUeId = event.currentTarget.dataset.ueId;
    // On instancie le modal bootstrap
    const modalElement = this.modalTarget;
    const modalInstance = new Modal(modalElement);
    // Affiche le modal
    modalInstance.show();
  }

  // Requete AJAX pour ajouter un etudiant à l'ue
  ajouterEtudiant() {
    // Recuparation de l'id de l'etudiant selectionné
    const etudiantId = this.etudiantSelectTarget.value;

    // appel AJAX de type POST pour ajouter l'etudiant à l'ue
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
      // On recharge la page si l'ajout a été effectué avec succès
      if (data.success) {
        window.location.reload();
        // Sinon on affiche un message d'erreur
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
        // On lie le lien pour modifier l'ue
        link.addEventListener('click', (e) => this.openEditModal(e));
      } else if (link.href.includes('/ue/admin/') && !link.href.includes('/edit') && !link.href.includes('/new')) {
        // On lie le lien pour voir l'ue
        link.addEventListener('click', (e) => this.openShowModal(e));
      }
    });
  }

  // Affiche le modal de detail de l'ue
  openShowModal(event) {
    // Empeche le chargement normal de la page
    event.preventDefault();
    // On recupere l'url du lien
    const url = event.currentTarget.href;
    // On instancie le modal bootstrap
    this.fetchModalContent(url, 'content');
  }

  // Affiche le modal modifier de l'ue
  openEditModal(event) {
    event.preventDefault();
    const url = event.currentTarget.href;
    this.fetchModalContent(url, 'form');
  }

  // Recupere et injecte le contenu du modal
  fetchModalContent(url, key) {
    fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(json => {
      if (json[key]) {
        // On verifie si le modal existe deja, sinon on le cree
        this.ensureModalExists();
        // Injecte le contenu du modal dans le modal bootstrap
        this.modalContentTarget.innerHTML = json[key];
        // Affiche le modal
        const modalInstance = new Modal(this.modalTarget);
        modalInstance.show();
        // Si c'est un formulaire, on bind la soumission 
        if (key === 'form') {
          this.bindFormSubmit(modalInstance);
        }
      }
    })
    .catch(err => console.error('Erreur AJAX:', err));
  }
  // Gestion de la soumission du formulaire dans le modal
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
            // Ferme le modal et recharge la page
            modalInstance.hide();
            window.location.reload();
          } else if (json.form) {
            // Reinjecte le formulaire dans le modal s'il y a une erreur
            this.modalContentTarget.innerHTML = json.form;
            this.bindFormSubmit(modalInstance);
          }
        })
        .catch(err => console.error('Erreur AJAX submit:', err));
    });
  }

  // Verifie si le modal existe deja, sinon on le cree
  ensureModalExists() {
    if (!this.hasModalTarget) {
      // On cree le modal bootstrap et l'inject dans le body
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
