// Sélectionnez la vidéo et l'overlay
const video = document.getElementById('myVideo');
const overlay = document.querySelector('.overlay');
const playButton = document.querySelector('.play-button');

// Fonction pour démarrer la lecture de la vidéo
playButton.addEventListener('click', () => {
  video.play();
  overlay.style.display = 'none'; // Masquer le bouton lorsque la vidéo démarre
});

// Lorsque la vidéo démarre
video.addEventListener('play', () => {
  overlay.style.display = 'none'; // Masquer l'overlay pendant la lecture
});

// Lorsque la vidéo est terminée
video.addEventListener('ended', () => {
  overlay.style.display = 'flex'; // Réafficher le bouton lorsque la vidéo est terminée
});


  