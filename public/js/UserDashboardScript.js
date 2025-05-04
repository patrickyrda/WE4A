console.log("UserDashboardScript.js loaded.");
document.addEventListener("DOMContentLoaded", async () => {
    const postsList = document.getElementById("posts-list");
  
    try {
      const res = await fetch("/user/api/get_news");
      const data = await res.json();
  
      if (!data || data.status !== "success" || !Array.isArray(data.data)) {
        postsList.innerHTML = "<p class='text-danger'>Erreur lors du chargement des articles.</p>";
        return;
      }
  
      if (data.data.length === 0) {
        postsList.innerHTML = "<p class='text-muted'>Aucun article disponible.</p>";
        return;
      }
  
      postsList.innerHTML = data.data.map(post => {
        const hasFile = post.file_path !== null;
        const nomFichier = hasFile ? post.file_path.split('/').pop() : '';
        const dateStr = new Date(post.date).toLocaleString('fr-FR', {
          day: '2-digit', month: '2-digit', year: 'numeric',
          hour: '2-digit', minute: '2-digit'
        });
  
        return `
          <article class="card shadow-sm mb-3">
            <div class="card-body">
              ${hasFile ? `
                <p class="mb-1">
                  <strong>${post.name} ${post.surname}</strong>
                  a posté un nouveau fichier,
                  <strong>${nomFichier}</strong>,
                  dans <strong>${post.code}</strong>.
                </p>` : `
                <p class="mb-1">
                  <strong>${post.name} ${post.surname}</strong>
                  a posté un nouveau message
                  <strong>${post.message.length > 60 ? post.message.slice(0, 60) + '…' : post.message}</strong>
                  dans <strong>${post.code}</strong>.
                </p>`}
              <small class="text-muted">${dateStr}</small>
            </div>
          </article>
        `;
      }).join('');
  
    } catch (err) {
      console.error("Erreur lors du chargement des articles:", err);
      postsList.innerHTML = "<p class='text-danger'>Erreur lors du chargement des articles.</p>";
    }
  });