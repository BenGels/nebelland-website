(function () {
  'use strict';

  var body = document.body;
  var toggle = document.querySelector('.nav-toggle');
  var groups = Array.prototype.slice.call(document.querySelectorAll('.main-nav__group'));

  // Mobiles Menü
  if (toggle) {
    toggle.addEventListener('click', function () {
      var open = body.classList.toggle('nav-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // Dropdowns (Desktop)
  function closeAll(except) {
    groups.forEach(function (g) {
      if (g !== except) {
        g.classList.remove('is-open');
        g.querySelector('.main-nav__trigger').setAttribute('aria-expanded', 'false');
      }
    });
  }

  groups.forEach(function (group) {
    var trigger = group.querySelector('.main-nav__trigger');
    trigger.addEventListener('click', function (event) {
      event.stopPropagation();
      var open = !group.classList.contains('is-open');
      closeAll(group);
      group.classList.toggle('is-open', open);
      trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });

  document.addEventListener('click', function () { closeAll(null); });
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeAll(null);
      if (body.classList.contains('nav-open')) {
        body.classList.remove('nav-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.focus();
      }
    }
  });

  // Zauberbuch-Filter nach Repräsentation
  var filter = document.querySelector('[data-spell-filter]');
  if (filter) {
    var buttons = Array.prototype.slice.call(filter.querySelectorAll('button'));
    var spells = Array.prototype.slice.call(document.querySelectorAll('.spell'));
    var circles = Array.prototype.slice.call(document.querySelectorAll('.circle'));

    buttons.forEach(function (button) {
      button.addEventListener('click', function () {
        var school = button.getAttribute('data-school');
        buttons.forEach(function (b) { b.setAttribute('aria-pressed', b === button ? 'true' : 'false'); });
        spells.forEach(function (spell) {
          var schools = spell.getAttribute('data-schools').split(' ');
          spell.hidden = school !== 'alle' && schools.indexOf(school) === -1;
        });
        circles.forEach(function (circle) {
          circle.hidden = !circle.querySelector('.spell:not([hidden])');
        });
      });
    });
  }
})();
