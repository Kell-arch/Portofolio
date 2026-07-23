/* ===== CUSTOM MODALS ===== */

function showAlert(message, type) {
  type = type || 'success';
  var overlay = document.createElement('div');
  overlay.className = 'modal-overlay';
  overlay.innerHTML =
    '<div class="modal-box">' +
    '<div class="modal-icon ' + type + '"><i class="fas fa-' + (type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'exclamation-triangle') + '"></i></div>' +
    '<h3>' + (type === 'success' ? 'Success' : type === 'error' ? 'Error' : 'Warning') + '</h3>' +
    '<p>' + message + '</p>' +
    '<div class="modal-actions"><button class="btn btn-primary btn-sm" onclick="closeModal(this)"><i class="fas fa-check"></i> OK</button></div>' +
    '</div>';
  document.body.appendChild(overlay);
  requestAnimationFrame(function () { overlay.classList.add('open'); });
}

function showConfirm(message, callback) {
  var overlay = document.createElement('div');
  overlay.className = 'modal-overlay';
  overlay.innerHTML =
    '<div class="modal-box">' +
    '<div class="modal-icon confirm"><i class="fas fa-question-circle"></i></div>' +
    '<h3>Confirm</h3>' +
    '<p>' + message + '</p>' +
    '<div class="modal-actions">' +
    '<button class="btn btn-outline btn-sm" onclick="closeModal(this)"><i class="fas fa-times"></i> Cancel</button>' +
    '<button class="btn btn-primary btn-sm" id="confirmYes"><i class="fas fa-check"></i> Yes</button>' +
    '</div>' +
    '</div>';
  document.body.appendChild(overlay);
  requestAnimationFrame(function () { overlay.classList.add('open'); });
  document.getElementById('confirmYes').addEventListener('click', function () {
    closeModal(this);
    if (typeof callback === 'function') callback();
  });
}

function closeModal(el) {
  var overlay = el.closest('.modal-overlay');
  if (overlay) {
    overlay.classList.remove('open');
    setTimeout(function () { overlay.remove(); }, 300);
  }
}

function showToast(message, type) {
  type = type || 'success';
  var container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  var icons = { success: 'check-circle', error: 'exclamation-circle', warning: 'exclamation-triangle' };

  var toast = document.createElement('div');
  toast.className = 'toast';
  toast.innerHTML =
    '<div class="toast-icon ' + type + '"><i class="fas fa-' + (icons[type] || 'info-circle') + '"></i></div>' +
    '<span class="toast-text">' + message + '</span>' +
    '<button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>';

  container.appendChild(toast);

  requestAnimationFrame(function () { toast.classList.add('show'); });

  setTimeout(function () {
    toast.classList.remove('show');
    setTimeout(function () { toast.remove(); }, 400);
  }, 4000);

  toast.querySelector('.toast-close').addEventListener('click', function () {
    toast.classList.remove('show');
    setTimeout(function () { toast.remove(); }, 400);
  });
}

document.addEventListener('DOMContentLoaded', function () {

  /* ===== TYPING EFFECT ===== */
  function initTyping() {
    var el = document.getElementById('typedText');
    if (!el) return;

    var words = ['Data Engineering Enthusiast', 'Python Developer', 'Problem Solver'];
    var wordIndex = 0;
    var charIndex = 0;
    var isDeleting = false;
    var isPaused = false;

    function type() {
      var current = words[wordIndex];

      if (isPaused) {
        isPaused = false;
        setTimeout(type, 1500);
        return;
      }

      if (isDeleting) {
        el.textContent = current.substring(0, charIndex - 1);
        charIndex--;
      } else {
        el.textContent = current.substring(0, charIndex + 1);
        charIndex++;
      }

      if (!isDeleting && charIndex === current.length) {
        isPaused = true;
        isDeleting = true;
        setTimeout(type, 2000);
        return;
      }

      if (isDeleting && charIndex === 0) {
        isDeleting = false;
        wordIndex = (wordIndex + 1) % words.length;
        isPaused = true;
        setTimeout(type, 500);
        return;
      }

      setTimeout(type, isDeleting ? 40 : 80);
    }

    type();
  }

  initTyping();

  /* ===== STICKY NAVBAR ===== */
  function initNavbar() {
    var navbar = document.getElementById('navbar');
    if (!navbar) return;

    window.addEventListener('scroll', function () {
      navbar.classList.toggle('scrolled', window.pageYOffset > 50);
    });
  }

  initNavbar();

  /* ===== MOBILE NAV TOGGLE ===== */
  function initMobileNav() {
    var toggle = document.getElementById('navToggle');
    var links = document.getElementById('navLinks');
    var overlay = document.getElementById('navOverlay');
    if (!toggle || !links || !overlay) return;

    function closeNav() {
      links.classList.remove('open');
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    }

    function openNav() {
      links.classList.add('open');
      overlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    }

    toggle.addEventListener('click', function () {
      links.classList.contains('open') ? closeNav() : openNav();
    });

    overlay.addEventListener('click', closeNav);

    document.querySelectorAll('.nav-links a').forEach(function (link) {
      link.addEventListener('click', closeNav);
    });
  }

  initMobileNav();

  /* ===== SCROLL REVEAL ===== */
  function initScrollReveal() {
    var elements = document.querySelectorAll('.fade-in, .fade-in-up, .fade-in-left, .fade-in-right');
    if (elements.length === 0) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    elements.forEach(function (el) { observer.observe(el); });
  }

  initScrollReveal();

  /* ===== SKILL BAR ANIMATION ===== */
  function initSkillBars() {
    var fills = document.querySelectorAll('.skill-bar .fill');
    if (fills.length === 0) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var fill = entry.target;
          var width = fill.getAttribute('data-width');
          if (width) fill.style.width = width + '%';
          observer.unobserve(fill);
        }
      });
    }, { threshold: 0.5 });

    fills.forEach(function (fill) { observer.observe(fill); });
  }

  initSkillBars();

  /* ===== BACK TO TOP ===== */
  function initBackToTop() {
    var btn = document.getElementById('backToTop');
    if (!btn) return;

    window.addEventListener('scroll', function () {
      btn.classList.toggle('visible', window.pageYOffset > 400);
    });

    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  initBackToTop();

  /* ===== FORM VALIDATION ===== */
  function initFormValidation() {
    var form = document.getElementById('contactForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var isValid = true;

      var fields = [
        { id: 'name', validation: function (v) { return v.trim().length >= 2; } },
        { id: 'email', validation: function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); } },
        { id: 'subject', validation: function (v) { return v.trim().length >= 3; } },
        { id: 'message', validation: function (v) { return v.trim().length >= 10; } }
      ];

      fields.forEach(function (field) {
        var input = document.getElementById(field.id);
        var group = input.closest('.form-group');
        var value = input.value;

        if (field.validation(value)) {
          group.classList.remove('error');
        } else {
          group.classList.add('error');
          isValid = false;
        }

        input.addEventListener('input', function () {
          if (field.validation(input.value)) group.classList.remove('error');
        });
      });

      if (isValid) {
        var btn = form.querySelector('button[type="submit"]');
        var originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        btn.disabled = true;

        setTimeout(function () {
          btn.innerHTML = originalText;
          btn.style.background = '';
          btn.style.borderColor = '';
          btn.disabled = false;
          form.reset();
          showToast('Your message has been sent successfully! I will get back to you soon.', 'success');
        }, 1500);
      }
    });
  }

  initFormValidation();

  /* ===== PROJECTS FILTER ===== */
  function initProjectFilter() {
    var searchInput = document.getElementById('projectSearch');
    var tagButtons = document.querySelectorAll('.filter-tag');
    var cards = document.querySelectorAll('.projects-grid .project-card');
    if (cards.length === 0) return;

    var activeTag = null;

    function filterProjects() {
      var query = searchInput ? searchInput.value.toLowerCase().trim() : '';

      cards.forEach(function (card) {
        var title = (card.querySelector('h3') || {}).textContent || '';
        var tags = Array.from(card.querySelectorAll('.project-tags span')).map(function (s) { return s.textContent.toLowerCase(); });
        var desc = (card.querySelector('p') || {}).textContent || '';

        var matchesSearch = !query || title.toLowerCase().includes(query) || desc.toLowerCase().includes(query) || tags.some(function (t) { return t.includes(query); });
        var matchesTag = !activeTag || tags.some(function (t) { return t === activeTag; });

        card.classList.toggle('hidden', !(matchesSearch && matchesTag));
      });
    }

    if (searchInput) {
      searchInput.addEventListener('input', filterProjects);
    }

    tagButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var tag = this.getAttribute('data-tag');

        if (this.classList.contains('active')) {
          this.classList.remove('active');
          activeTag = null;
        } else {
          tagButtons.forEach(function (b) { b.classList.remove('active'); });
          this.classList.add('active');
          activeTag = tag;
        }

        filterProjects();
      });
    });
  }

  initProjectFilter();

  /* ===== THEME TOGGLE ===== */
  function initThemeToggle() {
    var toggle = document.getElementById('themeToggle');
    if (!toggle) return;

    var html = document.documentElement;
    var stored = localStorage.getItem('theme') || 'dark';

    var themes = ['dark', 'light', 'glass'];
    var icons = {
      dark: '<i class="fas fa-moon"></i>',
      light: '<i class="fas fa-sun"></i>',
      glass: '<i class="fas fa-window-maximize"></i>'
    };

    function applyTheme(theme) {
      html.classList.remove('light', 'glass');
      if (theme !== 'dark') html.classList.add(theme);
      toggle.innerHTML = icons[theme] || icons.dark;
      localStorage.setItem('theme', theme);
    }

    if (themes.indexOf(stored) !== -1) {
      applyTheme(stored);
    }

    toggle.addEventListener('click', function () {
      var current = 'dark';
      if (html.classList.contains('light')) current = 'light';
      else if (html.classList.contains('glass')) current = 'glass';

      var idx = themes.indexOf(current);
      var next = themes[(idx + 1) % themes.length];
      applyTheme(next);
    });
  }

  initThemeToggle();

  /* ===== STATS COUNTER ===== */
  function initStatsCounter() {
    var counters = document.querySelectorAll('.stat-number');
    if (counters.length === 0) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var el = entry.target;
          var target = parseInt(el.getAttribute('data-target'), 10);
          if (isNaN(target)) return;
          var duration = 1500;
          var startTime = null;

          function animate(time) {
            if (!startTime) startTime = time;
            var elapsed = time - startTime;
            var progress = Math.min(elapsed / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(eased * target);
            if (progress < 1) requestAnimationFrame(animate);
          }

          observer.unobserve(el);
          requestAnimationFrame(animate);
        }
      });
    }, { threshold: 0.3 });

    counters.forEach(function (el) { observer.observe(el); });
  }

  initStatsCounter();

});
