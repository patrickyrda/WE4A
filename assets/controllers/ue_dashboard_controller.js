import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["modal", "etudiantSelect"];

  connect() {
    console.log('Stimulus fonctionne !');
    this.currentUeId = null;
  }

  ouvrirModal(event) {
    console.log('Bouton cliqué !');
    this.currentUeId = event.currentTarget.dataset.ueId;
    const modalElement = this.modalTarget;
    const modalInstance = new bootstrap.Modal(modalElement);
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
}
