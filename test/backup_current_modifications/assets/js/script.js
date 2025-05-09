// Sélectionnez le bouton du menu utilisateur
const menuIcon = document.querySelector('.menu-icon');

// Créez un menu déroulant
menuIcon.addEventListener('click', () => {
    const dropdown = document.createElement('div');
    dropdown.className = 'dropdown-menu';
    dropdown.innerHTML = `
        <a href="#">Profil</a>
        <a href="#">Paramètres</a>
        <a href="#">Déconnexion</a>
    `;
    document.body.appendChild(dropdown);

    // Positionnez le menu sous le bouton
    const rect = menuIcon.getBoundingClientRect();
    dropdown.style.position = 'absolute';
    dropdown.style.top = `${rect.bottom}px`;
    dropdown.style.right = `${window.innerWidth - rect.right}px`;

    // Fermez le menu si on clique ailleurs
    document.addEventListener('click', (e) => {
        if (!menuIcon.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.remove();
        }
    });
});