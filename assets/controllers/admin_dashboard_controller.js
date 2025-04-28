import { Controller } from '@hotwired/stimulus'
import { Modal } from 'bootstrap'

export default class extends Controller {
  static targets = ['ueContainer', 'userContainer', 'modal', 'modalContent','btnUe', 'btnStudent'];
  static values = { active: String };
  
  connect () {
    if (!this.hasActiveValue) this.activeValue = 'ue';
+   this._updateButtons();
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

  #fetchAndInject (url, targetName) {
    if (targetName === 'ueContainer') {
      this.userContainerTarget.innerHTML = ''
    } else {
      this.ueContainerTarget.innerHTML = ''
    }

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => {
        if (!r.ok) throw new Error(`Status ${r.status}`)
        return r.json()
      })
      .then(json => {
        if (json.success) {
          this[`${targetName}Target`].innerHTML = json.html
          if (targetName === 'userContainer') {
            this.#bindUserAdminLinks()
          }
        }
      })
      .catch(err => console.error('Erreur AJAX', err))
  }

  #bindUserAdminLinks () {
    document.querySelectorAll('a[href]').forEach(link => {
      const href = link.getAttribute('href')
      if (href?.startsWith('/user/admin/') && href.includes('/edit')) {
        link.addEventListener('click', e => this.openEditModal(e))
      } else if (href?.startsWith('/user/admin/') && !href.includes('/edit') && !href.includes('/new')) {
        link.addEventListener('click', e => this.openShowModal(e))
      }
    })
  }

  openShowModal (event) {
    event.preventDefault()
    this.#fetchModalContent(event.currentTarget.href, 'content')
  }

  openEditModal (event) {
    event.preventDefault()
    this.#fetchModalContent(event.currentTarget.href, 'form')
  }

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
            this.modalContentTarget.innerHTML = json.form
            this.#bindFormSubmit(modal)
          }
        })
        .catch(err => console.error('Erreur AJAX', err))
    })
  }

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
  _updateButtons() {
    const isUe = this.activeValue === 'ue';
    this.btnUeTarget.classList.toggle('btn-primary', isUe);
    this.btnUeTarget.classList.toggle('btn-outline-primary', !isUe);
    this.btnStudentTarget.classList.toggle('btn-primary', !isUe);
    this.btnStudentTarget.classList.toggle('btn-outline-primary', isUe);
    }
}