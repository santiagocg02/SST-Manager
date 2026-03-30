(function () {
  function normalizeButton(button, type) {
    if (!button) return;

    button.classList.remove('btn-ui', 'btn-sst', 'btn-sst-outline', 'btn-primary-lite', 'primary', 'secondary');
    button.classList.add('btn', 'btn-sm');

    if (type === 'back') {
      button.classList.remove('btn-primary', 'btn-success');
      button.classList.add('btn-secondary');
      button.textContent = 'Volver';
      return;
    }

    if (type === 'save') {
      button.classList.remove('btn-primary', 'btn-secondary');
      button.classList.add('btn-success');
      const plain = button.textContent.replace(/\s+/g, ' ').trim().toLowerCase();
      if (!plain.includes('guardar')) {
        button.textContent = 'Guardar';
      }
      if (!button.querySelector('.fa-save')) {
        button.innerHTML = '<i class="fa-solid fa-save"></i> Guardar';
      }
      return;
    }

    if (type === 'print') {
      button.classList.remove('btn-success', 'btn-secondary');
      button.classList.add('btn-primary');
      if (!button.querySelector('.fa-print')) {
        button.innerHTML = '<i class="fa-solid fa-print"></i> Imprimir';
      } else {
        button.innerHTML = '<i class="fa-solid fa-print"></i> Imprimir';
      }
    }
  }

  function findToolbar() {
    return document.querySelector('.sst-toolbar')
      || document.querySelector('.toolbar')
      || document.querySelector('.topbar');
  }

  function findTitle(toolbar) {
    return toolbar.querySelector('.sst-toolbar-title')
      || toolbar.querySelector('.toolbar-title')
      || toolbar.querySelector('h1')
      || toolbar.querySelector('h2');
  }

  function getOrCreateActions(toolbar, title) {
    let actions = toolbar.querySelector('.sst-toolbar-actions')
      || toolbar.querySelector('.actions')
      || toolbar.querySelector('.toolbar-right')
      || toolbar.querySelector('.topbar-right');

    if (!actions) {
      actions = document.createElement('div');
      actions.className = 'sst-toolbar-actions';
      toolbar.appendChild(actions);
    }

    if (actions.classList.contains('toolbar-right') || actions.classList.contains('topbar-right')) {
      actions.classList.add('sst-toolbar-actions');
    }

    if (title && !toolbar.contains(title)) {
      toolbar.prepend(title);
    }

    return actions;
  }

  function findButton(selectorList, root) {
    for (const selector of selectorList) {
      const el = root.querySelector(selector);
      if (el) return el;
    }
    return null;
  }

  document.addEventListener('DOMContentLoaded', function () {
    const toolbar = findToolbar();
    if (!toolbar) return;

    toolbar.classList.add('sst-toolbar');

    const title = findTitle(toolbar);
    if (title) {
      title.classList.add('sst-toolbar-title');
    }

    const actions = getOrCreateActions(toolbar, title);

    const backBtn = findButton([
      'a[href*="planear"]',
      'a[href*="../"]',
      'a.btn-outline-secondary',
      'a.btn-outline-primary'
    ], toolbar);

    const saveBtn = findButton([
      '#btnGuardar',
      'button[data-action="guardar"]',
      'button.btn-success',
      'button[onclick*="guardar"]'
    ], toolbar);

    const printBtn = findButton([
      'button[onclick*="print"]',
      'button.btn-primary',
      'button[data-action="print"]'
    ], toolbar);

    if (backBtn) normalizeButton(backBtn, 'back');
    if (saveBtn) normalizeButton(saveBtn, 'save');
    if (printBtn) normalizeButton(printBtn, 'print');

    [backBtn, saveBtn, printBtn].forEach(function (btn) {
      if (btn) actions.appendChild(btn);
    });
  });
})();
