document.addEventListener('DOMContentLoaded', () => {
    const welcomeMessage = document.getElementById('welcome-message');
    const content = document.getElementById('content');
    const title = document.getElementById('title');
    const text = document.getElementById('text');
    const buttons = document.getElementById('buttons');

    setTimeout(() => {
        welcomeMessage.style.display = 'none';
        content.style.display = 'block';
        title.style.opacity = 1;
        setTimeout(() => {
            // Display text with animation
            title.style.animation = "slideDown 2s ease-out";  // Keep only slideDown animation
            text.classList.add('text-appear');
            displayText();
        }, 2100);  // Time to let the shaking animation run before stopping
    }, 5100);

    function displayText() {
        const textContent = "Simen Lespwa, une mise en commun des anciens élèves de l'école Wesléyenne de Mare-Sucrin éparpillés à travers tous les coins de la terre et d'autres amis soucieux de l'avenir des enfants d'aujourd'hui et adultes de demain, collecte des fonds pour aider les enfants nécessiteux avec les frais scolaires, uniformes et matériels scolaires. Cette école fondée en 1960 a formé pas mal de générations de la meme annee qui donnent leurs services dans plusieurs continents. Des cadres dans plusieurs domaines au service du monde soucieux de la formation de base reçue de cette école, ont décidé en Janvier 2022 que ses portes soient reouvertes en Octobre de la même par les fonds recueillis des fils conscients du rôle qu'a joué cette école dans leur vie quotidienne et professionnelle.";
        text.textContent = textContent;
        // Optionally, you can add an additional delay before showing buttons
        setTimeout(() => {
            buttons.style.display = 'flex';
            Array.from(buttons.children).forEach((button, index) => {
                button.style.animationDelay = `${index * 0.8}s`;
            });
        }, 510);  // Delay before showing buttons
    }
});
//dezyem konpatiman
const sliderContainer = document.querySelector('.slider-container');
const prevButton = document.querySelector('.prev');
const nextButton = document.querySelector('.next');
const cards = document.querySelectorAll('.card');

let currentPosition = 0;
const totalCards = cards.length;
const cardsPerView = 3; // Number of cards visible at once

function updateButtons() {
  if (currentPosition === 0) {
    prevButton.style.display = 'none'; // Hide 'Prev' button if on the first set of cards
  } else {
    prevButton.style.display = 'block'; // Show 'Prev' button otherwise
  }

  if (currentPosition >= totalCards - cardsPerView) {
    nextButton.style.display = 'none'; // Hide 'Next' button if on the last set of cards
  } else {
    nextButton.style.display = 'block'; // Show 'Next' button otherwise
  }
}

function slideNext() {
  if (currentPosition < totalCards - cardsPerView) {
    currentPosition++;
    sliderContainer.style.transform = `translateX(-${currentPosition * 100 / cardsPerView}%)`;
  }
  updateButtons();
}

function slidePrev() {
  if (currentPosition > 0) {
    currentPosition--;
    sliderContainer.style.transform = `translateX(-${currentPosition * 100 / cardsPerView}%)`;
  }
  updateButtons();
}

// Event listeners
nextButton.addEventListener('click', slideNext);
prevButton.addEventListener('click', slidePrev);

// Initial setup
updateButtons();
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


