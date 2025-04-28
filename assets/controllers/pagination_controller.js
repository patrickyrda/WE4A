import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['wrapper'];

  changePage(event) {
    const link = event.target.closest('a.page-link');
    if (!link) return;
    if (link.parentElement.classList.contains('disabled')) return;

    event.preventDefault();
    const url = link.href;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(resp => resp.text())
      .then(html => {
        this.wrapperTarget.innerHTML = html;
        this.wrapperTarget.scrollIntoView({ behavior: 'smooth', block: 'start' });
      })
      .catch(console.error);
  }
}