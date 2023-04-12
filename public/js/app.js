let compteur = 0;
let timer, elements, slides, slideWidth;

window.onload = () => {
  const slider = document.querySelector('.slider');

  elements = document.querySelector('.elements');

  let firstImage = elements.firstElementChild.cloneNode(true);

  elements.appendChild(firstImage);

  slides = Array.from(elements.children);

  slideWidth = slider.getBoundingClientRect().width;

  const next = document.querySelector('#nav-droite');
  const previous = document.querySelector('#nav-gauche');

  next.addEventListener('click', slideNext);
  previous.addEventListener('click', slidePrevious);

  timer = setInterval(slideNext, 5000);
}

// défilement vers la droite
function slideNext(){
  compteur++;
  elements.style.transition = "ease-in-out 0.7s";

  let decal = -slideWidth * compteur;
  elements.style.transform = `translateX(${decal}px)`;

  setTimeout(function(){
    if(compteur >= slides.length - 1){
      compteur = 0;
      elements.style.transition = "unset";
      elements.style.transform = "translateX(0)";
    }
  }, 700)
}

// défilement vers la gauche
function slidePrevious(){
  compteur--;
  elements.style.transition = "ease-in-out 0.7s";

  if(compteur < 0){
    compteur = slides.length - 1;
    let decal = -slideWidth * compteur;
    elements.style.transition = "unset";
    elements.style.transform = `translateX(${decal}px)`;
    setTimeout(slidePrevious, 1);
  }

  let decal = -slideWidth * compteur;
  elements.style.transform = `translateX(${decal}px)`;
}