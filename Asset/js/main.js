const header = document.querySelector('.site-header');
const toggle = document.querySelector('.nav-toggle');

if (header && toggle) {
  toggle.addEventListener('click', () => {
    const isOpen = header.classList.toggle('nav-open');
    toggle.setAttribute('aria-expanded', String(isOpen));
  });
}

const heroCards = document.querySelectorAll('.card');
heroCards.forEach(card => {
  card.addEventListener('mouseenter', () => card.classList.add('is-hover'));
  card.addEventListener('mouseleave', () => card.classList.remove('is-hover'));
});

const scrollButton = document.querySelector('[data-scroll-top]');
if (scrollButton) {
  window.addEventListener('scroll', () => {
    scrollButton.classList.toggle('visible', window.scrollY > 500);
  });

  scrollButton.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}
