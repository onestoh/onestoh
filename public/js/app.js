/* EstateYard — Main JavaScript */

document.addEventListener('DOMContentLoaded', function () {

  // ===== ANIMATED COUNTERS =====
  const counters = document.querySelectorAll('[data-counter]');
  counters.forEach(counter => {
    const target = parseInt(counter.getAttribute('data-counter'));
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;
    const timer = setInterval(() => {
      current += step;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      counter.textContent = Math.floor(current).toLocaleString();
    }, 16);
  });

  // ===== SCROLL ANIMATIONS =====
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  document.querySelectorAll('.property-card, .feature-card, .kpi-card, .stakeholder-card, .category-card').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    observer.observe(el);
  });

  // ===== SAVE / WISHLIST TOGGLE =====
  document.querySelectorAll('.property-card-save').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const saved = this.getAttribute('data-saved') === 'true';
      this.setAttribute('data-saved', !saved);
      this.innerHTML = saved ? '♡' : '♥';
      this.style.color = saved ? '' : 'var(--red)';
    });
  });

  // ===== REFERRAL LINK COPY =====
  document.querySelectorAll('[data-copy]').forEach(btn => {
    btn.addEventListener('click', function() {
      const text = this.getAttribute('data-copy');
      navigator.clipboard.writeText(text).then(() => {
        const orig = this.textContent;
        this.textContent = '✓ Copied!';
        this.style.color = 'var(--green)';
        setTimeout(() => {
          this.textContent = orig;
          this.style.color = '';
        }, 2000);
      });
    });
  });

  // ===== SEARCH AUTOSUGGEST =====
  const searchInputs = document.querySelectorAll('.search-input');
  const suggestions = ['Karen, Nairobi', 'Westlands', 'Kilimani', 'Upper Hill', 'Lavington', 'Runda', 'Muthaiga', 'Mombasa', 'Nakuru', 'Kisumu', 'Thika'];

  searchInputs.forEach(input => {
    let suggestionBox;

    input.addEventListener('input', function() {
      const val = this.value.toLowerCase();
      if (!val || val.length < 2) {
        if (suggestionBox) suggestionBox.remove();
        return;
      }
      const matches = suggestions.filter(s => s.toLowerCase().includes(val));
      if (!matches.length) {
        if (suggestionBox) suggestionBox.remove();
        return;
      }

      if (!suggestionBox) {
        suggestionBox = document.createElement('div');
        suggestionBox.style.cssText = 'position:absolute; top:100%; left:0; right:0; background:var(--navy2); border:1px solid var(--border); border-radius:0 0 12px 12px; z-index:100; overflow:hidden;';
        this.parentElement.style.position = 'relative';
        this.parentElement.appendChild(suggestionBox);
      }

      suggestionBox.innerHTML = matches.map(m =>
        `<div style="padding:10px 20px; cursor:pointer; font-size:14px; color:var(--text); border-bottom:1px solid rgba(255,255,255,0.04);" onmouseover="this.style.background='var(--gold-dim)'" onmouseout="this.style.background='transparent'" onclick="document.querySelector('.search-input').value='${m}'; this.closest('div[style]').remove();">📍 ${m}</div>`
      ).join('');
    });

    document.addEventListener('click', (e) => {
      if (!e.target.closest('.search-bar') && suggestionBox) {
        suggestionBox.remove();
        suggestionBox = null;
      }
    });
  });

  // ===== AUCTION COUNTDOWN =====
  function updateCountdowns() {
    document.querySelectorAll('[data-countdown]').forEach(el => {
      const endTime = parseInt(el.getAttribute('data-countdown'));
      const now = Date.now() / 1000;
      const remaining = endTime - now;

      if (remaining <= 0) {
        el.innerHTML = '<span style="color:var(--red);">ENDED</span>';
        return;
      }

      const d = Math.floor(remaining / 86400);
      const h = Math.floor((remaining % 86400) / 3600);
      const m = Math.floor((remaining % 3600) / 60);
      const s = Math.floor(remaining % 60);

      if (el.classList.contains('countdown-timer')) {
        el.innerHTML = [
          d > 0 ? `<div class="countdown-unit"><span class="countdown-num">${String(d).padStart(2,'0')}</span><span class="countdown-label">Days</span></div>` : '',
          `<div class="countdown-unit"><span class="countdown-num">${String(h).padStart(2,'0')}</span><span class="countdown-label">Hours</span></div>`,
          `<div class="countdown-unit"><span class="countdown-num">${String(m).padStart(2,'0')}</span><span class="countdown-label">Mins</span></div>`,
          `<div class="countdown-unit"><span class="countdown-num">${String(s).padStart(2,'0')}</span><span class="countdown-label">Secs</span></div>`
        ].join('');
      } else {
        el.textContent = d > 0 ? `${d}d ${h}h` : `${h}h ${m}m`;
      }
    });
  }

  setInterval(updateCountdowns, 1000);
  updateCountdowns();

  // ===== TABS =====
  document.querySelectorAll('.tab-item').forEach(tab => {
    tab.addEventListener('click', function(e) {
      if (this.getAttribute('href') === '#') {
        e.preventDefault();
      }
      const tabGroup = this.closest('.tabs');
      if (!tabGroup) return;
      tabGroup.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
    });
  });

  // ===== CHART BARS ANIMATION =====
  document.querySelectorAll('.chart-bar[data-height]').forEach(bar => {
    const height = bar.getAttribute('data-height');
    setTimeout(() => {
      bar.style.height = height;
    }, 300);
  });

  // ===== TOAST NOTIFICATIONS =====
  window.showToast = function(message, type = 'gold') {
    const toast = document.createElement('div');
    const colors = {
      gold: ['rgba(212,168,67,0.15)', 'rgba(212,168,67,0.3)', 'var(--gold)'],
      green: ['rgba(46,204,138,0.15)', 'rgba(46,204,138,0.3)', 'var(--green)'],
      red: ['rgba(224,82,82,0.15)', 'rgba(224,82,82,0.3)', 'var(--red)'],
      blue: ['rgba(74,159,224,0.15)', 'rgba(74,159,224,0.3)', 'var(--blue)'],
    };
    const [bg, border, color] = colors[type] || colors.gold;

    toast.style.cssText = `
      position:fixed; bottom:24px; right:24px; z-index:9999;
      background:${bg}; border:1px solid ${border}; color:${color};
      border-radius:10px; padding:14px 20px; font-size:14px;
      box-shadow:var(--shadow); max-width:360px;
      animation:fadeInUp .3s ease both;
      display:flex; align-items:center; gap:10px;
    `;
    toast.innerHTML = `<span>${type === 'green' ? '✅' : type === 'red' ? '⚠️' : type === 'blue' ? 'ℹ️' : '✨'}</span> ${message}`;
    document.body.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(20px)';
      toast.style.transition = 'all .3s';
      setTimeout(() => toast.remove(), 300);
    }, 4000);
  };

  // ===== MOBILE FILTER DRAWER =====
  const filterToggle = document.getElementById('filterToggle');
  const filterDrawer = document.getElementById('filterDrawer');
  if (filterToggle && filterDrawer) {
    filterToggle.addEventListener('click', () => {
      filterDrawer.style.display = filterDrawer.style.display === 'block' ? 'none' : 'block';
    });
  }

  // ===== FORM VALIDATION =====
  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', function(e) {
      let valid = true;
      this.querySelectorAll('[required]').forEach(field => {
        if (!field.value.trim()) {
          valid = false;
          field.style.borderColor = 'var(--red)';
          field.addEventListener('input', () => field.style.borderColor = '', { once: true });
        }
      });
      if (!valid) {
        e.preventDefault();
        showToast('Please fill in all required fields', 'red');
      }
    });
  });

  // ===== RANGE SLIDER =====
  document.querySelectorAll('input[type="range"]').forEach(slider => {
    const output = document.getElementById(slider.getAttribute('data-output'));
    if (output) {
      slider.addEventListener('input', () => {
        output.textContent = '$' + parseInt(slider.value).toLocaleString();
      });
    }
  });

  // ===== SMOOTH SCROLL =====
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', function(e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

});
