import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["tabText","tabFile","sectionText","sectionFile"];
  static values = { initialTab: String }

  connect() {
    this.show(this.initialTabValue || "text");
  }

  show(event) {
    const type = typeof event === "string" ? event : event.currentTarget.dataset.postFormType;
    const isText = type === "text";

    this.sectionTextTarget.classList.toggle("d-none", !isText);
    this.sectionFileTarget.classList.toggle("d-none", isText);

    this.tabTextTarget.classList.toggle("btn-primary", isText);
    this.tabTextTarget.classList.toggle("btn-outline-primary", !isText);
    this.tabFileTarget.classList.toggle("btn-primary", !isText);
    this.tabFileTarget.classList.toggle("btn-outline-primary", isText);
  }
}
