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

window.apiFetch = async (url, options = {}) => {
  const token = localStorage.getItem('auth_token');
  const mergedOptions = { ...options };
  mergedOptions.headers = { ...(options.headers || {}) };
  if (token && !mergedOptions.headers.Authorization) {
    mergedOptions.headers.Authorization = `Bearer ${token}`;
  }
  const response = await fetch(url, mergedOptions);
  const contentType = response.headers.get('content-type') || '';
  let data = null;
  let text = null;

  if (contentType.includes('application/json')) {
    try {
      data = await response.json();
    } catch (error) {
      data = null;
    }
  } else {
    try {
      text = await response.text();
    } catch (error) {
      text = null;
    }
  }

  return { ok: response.ok, status: response.status, data, text };
};

window.getApiErrorMessage = (payload, fallback = 'Une erreur est survenue.') => {
  if (!payload) {
    return fallback;
  }
  if (payload.data && payload.data.error) {
    return payload.data.error;
  }
  if (payload.data && payload.data.message) {
    return payload.data.message;
  }
  if (payload.text && payload.text.trim()) {
    return 'Une erreur est survenue. Merci de réessayer.';
  }
  return fallback;
};

window.storeAuthToken = (token) => {
  if (token) {
    localStorage.setItem('auth_token', token);
  }
};

window.clearAuthToken = () => {
  localStorage.removeItem('auth_token');
};
