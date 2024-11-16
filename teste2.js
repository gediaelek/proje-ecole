// Fonction pour basculer entre les galeries (photos/vidéos)
function toggleGallery(type) {
    // Cacher toutes les galeries
    document.querySelector('#photos-gallery').style.display = 'none';
    document.querySelector('#videos-gallery').style.display = 'none';

    // Afficher la galerie correspondante
    if (type === 'photos') {
        document.querySelector('#photos-gallery').style.display = 'flex';
    } else if (type === 'videos') {
        document.querySelector('#videos-gallery').style.display = 'flex';
    }
}

// Initialiser la galerie des photos comme visible
toggleGallery('photos');
