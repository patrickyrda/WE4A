import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['wrapper'];

  // On cherche la page lors de l'appui sur un lien de pagination
  changePage(event) {
    // On cherche le lien de pagination cliqué
    const link = event.target.closest('a.page-link');
    // Ne fait rien si le lien n'est pas trouvé
    if (!link) return;
    // Ne fait rien si le lien est désactivé
    if (link.parentElement.classList.contains('disabled')) return;

    event.preventDefault();
    const url = link.href;

    // Fetch les nouveaux posts
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(resp => resp.text())
      .then(html => {
        this.wrapperTarget.innerHTML = html;
        this.wrapperTarget.scrollIntoView({ behavior: 'smooth', block: 'start' });
      })
      .catch(console.error);
  }
}