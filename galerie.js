// Fonction pour vérifier si un élément est visible dans la fenêtre
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return rect.top < window.innerHeight && rect.bottom > 0;
}

// Fonction pour gérer l'animation de tous les compartiments lors du défilement
function handleScrollAnimation() {
    const compartments = document.querySelectorAll('.animated-compartment');
    compartments.forEach(compartment => {
        if (isInViewport(compartment)) {
            compartment.classList.add('visible');
        }
    });
}

// Écouteur d'événement pour le défilement
window.addEventListener('scroll', handleScrollAnimation);

// Lancer l'animation à l'initialisation de la page
handleScrollAnimation();

document.getElementById('toggleGalleryButton').addEventListener('click', function() {
    const photoGallery = document.getElementById('photo-gallery');
    const videoGallery = document.getElementById('video-gallery');
    const toggleButton = document.getElementById('toggleGalleryButton');

    console.log("Bouton cliqué"); // Vérifiez que ce message s'affiche dans la console

    if (photoGallery.classList.contains('hidden')) {
        console.log("Affichage de la galerie de photos"); // Vérifiez cette ligne
        photoGallery.classList.remove('hidden');
        videoGallery.classList.add('hidden');
        toggleButton.textContent = 'Passer à la galerie vidéo';
    } else {
        console.log("Affichage de la galerie de vidéos"); // Vérifiez cette ligne
        photoGallery.classList.add('hidden');
        videoGallery.classList.remove('hidden');
        toggleButton.textContent = 'Passer à la galerie photo';
    }
});
