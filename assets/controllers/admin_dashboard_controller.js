import { Controller } from '@hotwired/stimulus'
import { Modal } from 'bootstrap'

export default class extends Controller {
  static targets = ['ueContainer', 'userContainer', 'modal', 'modalContent','btnUe', 'btnStudent'];
  static values = { active: String };
  
  connect () {
    // Si aucune valeur active n'est définie, on initialise à 'ue'
    if (!this.hasActiveValue) this.activeValue = 'ue';
    // this._updateButtons();
    // Chargement des UEs par défaut
    this._fetchAndInject('/ue/admin', 'ueContainer');
  }

  loadUeTable (event) {
    if (event) event.preventDefault()
    this.#fetchAndInject('/ue/admin', 'ueContainer')
  }

  loadUserTable (event) {
    event.preventDefault()
    this.#fetchAndInject('/user/admin', 'userContainer')
  }

  //Effectue requête AJAX et injecte le contenu dans la cible
  #fetchAndInject (url, targetName) {
    // Vide le contenu de l'autre conteneur pour éviter les conflits
    if (targetName === 'ueContainer') {
      this.userContainerTarget.innerHTML = ''
    } else {
      this.ueContainerTarget.innerHTML = ''
    }
    // Effectue une requête AJAX pour récupérer les données
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => {
        if (!r.ok) throw new Error(`Status ${r.status}`)
        return r.json()
      })
      .then(json => {
        if (json.success) {
          // Injecte le contenu HTML dans la cible
          this[`${targetName}Target`].innerHTML = json.html
          // Vérifie si le conteneur est celui des utilisateurs
          if (targetName === 'userContainer') {
            this.#bindUserAdminLinks()
          }
        }
      })
      .catch(err => console.error('Erreur AJAX', err))
  }

  // Lie les liens d'administration des utilisateurs aux événements d'ouverture de modal
  #bindUserAdminLinks () {
    document.querySelectorAll('a[href]').forEach(link => {
      const href = link.getAttribute('href')
      // Si le lien est pour l'édition d'un utilisateur
      if (href?.startsWith('/user/admin/') && href.includes('/edit')) {
        link.addEventListener('click', e => this.openEditModal(e))
      }
      // Si le lien est pour l'affichage d'un utilisateur 
      else if (href?.startsWith('/user/admin/') && !href.includes('/edit') && !href.includes('/new')) {
        link.addEventListener('click', e => this.openShowModal(e))
      }
    })
  }

  // Modal d'affichage
  openShowModal (event) {
    event.preventDefault()
    this.#fetchModalContent(event.currentTarget.href, 'content')
  }

  // Modal d'edition
  openEditModal (event) {
    event.preventDefault()
    this.#fetchModalContent(event.currentTarget.href, 'form')
  }

  // Récupere le contenu d'un modal via AJAX
  #fetchModalContent (url, key) {
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(json => {
        if (json[key]) {
          this.#ensureModalExists()
          this.modalContentTarget.innerHTML = json[key]
          const modal = new Modal(this.modalTarget)
          modal.show()

          if (key === 'form') this.#bindFormSubmit(modal)
        }
      })
      .catch(err => console.error('Erreur AJAX', err))
  }

  // Lie le formulaire de soumission dans le modal
  #bindFormSubmit (modal) {
    const form = this.modalContentTarget.querySelector('form')
    if (!form) return

    form.addEventListener('submit', e => {
      e.preventDefault()
      const formData = new FormData(form)

      fetch(form.action, { method: form.method, body: formData })
        .then(r => r.json())
        .then(json => {
          if (json.success) {
            modal.hide()
            this.loadUserTable(new Event('dummy'))
          } else if (json.form) {
            // Injecte le nouveau contenu du formulaire dans le modal
            this.modalContentTarget.innerHTML = json.form
            this.#bindFormSubmit(modal)
          }
        })
        .catch(err => console.error('Erreur AJAX', err))
    })
  }

  // Vérifie si le modal existe déjà
  #ensureModalExists () {
    if (!this.hasModalTarget) {
      const modal = document.createElement('div')
      modal.className = 'modal fade'
      modal.id = 'modalAdmin'
      modal.tabIndex = -1
      modal.innerHTML = `
        <div class="modal-dialog">
          <div class="modal-content" data-admin-dashboard-target="modalContent"></div>
        </div>`
      document.body.appendChild(modal)
      this.modalTarget = modal
      this.modalContentTarget = modal.querySelector('[data-admin-dashboard-target="modalContent"]')
    }
  }

  // Pas encore utiliser
  // _updateButtons() {
  //   const isUe = this.activeValue === 'ue';
  //   this.btnUeTarget.classList.toggle('btn-primary', isUe);
  //   this.btnUeTarget.classList.toggle('btn-outline-primary', !isUe);
  //   this.btnStudentTarget.classList.toggle('btn-primary', !isUe);
  //   this.btnStudentTarget.classList.toggle('btn-outline-primary', isUe);
  //   }
}