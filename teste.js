// script.js
const switchButton = document.getElementById('switch-button');
const photosSection = document.querySelector('.photos');
const videosSection = document.querySelector('.videos');

let showingPhotos = true; // Indique si les photos sont affichées

switchButton.addEventListener('click', () => {
  if (showingPhotos) {
    // Afficher les vidéos
    photosSection.classList.remove('active');
    videosSection.classList.add('active');
    switchButton.textContent = 'Passer aux Photos';
  } else {
    // Afficher les photos
    videosSection.classList.remove('active');
    photosSection.classList.add('active');
    switchButton.textContent = 'Passer aux Vidéos';
  }
  showingPhotos = !showingPhotos; // Basculer l'état
});
