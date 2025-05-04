import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  // Définition des cibles et des valeurs à manipuler
  static targets = ["tabText","tabFile","sectionText","sectionFile"];
  static values = { initialTab: String }

  connect() {
    // On initialise le contrôleur en affichant la section de texte ou de fichier selon la valeur initiale
    this.show(this.initialTabValue || "text");
  }

  // Méthode pour changer d'onglet et afficher la section correspondante
  show(event) {
    // On récupère le type d'onglet (texte ou fichier) pour basculer entre les onglets
    const type = typeof event === "string" 
    ? event 
    : event.currentTarget.dataset.postFormType;
    // Verifie si le type de l'onglet est "text"
    const isText = type === "text";

    // On affiche la section correspondante et on cache l'autre
    this.sectionTextTarget.classList.toggle("d-none", !isText);
    this.sectionFileTarget.classList.toggle("d-none", isText);

    // Bascule les styles actifs et inactifs des onglets
    this.tabTextTarget.classList.toggle("btn-primary", isText);
    this.tabTextTarget.classList.toggle("btn-outline-primary", !isText);
    this.tabFileTarget.classList.toggle("btn-primary", !isText);
    this.tabFileTarget.classList.toggle("btn-outline-primary", isText);
  }
}