(function () {
    'use strict';
    function start() {
        document.querySelectorAll('[data-iagrl-section]').forEach(function (section) {
            if (section.dataset.iagrlReady === 'true') return;
            section.dataset.iagrlReady = 'true';
            var items = Array.prototype.slice.call(section.querySelectorAll('[data-iagrl-card]'));
            var chips = Array.prototype.slice.call(section.querySelectorAll('[data-iagrl-filter]'));
            var input = section.querySelector('[data-iagrl-search]');
            var result = section.querySelector('[data-iagrl-results]');
            var empty = section.querySelector('[data-iagrl-empty]');
            var more = section.querySelector('[data-iagrl-more]');
            var reset = section.querySelector('[data-iagrl-reset]');
            var limit = Math.max(1, parseInt(section.dataset.iagrlLimit, 10) || 6);
            var shown = limit;
            var filter = 'all';

            function normalize(value) { return (value || '').toLocaleLowerCase().trim(); }
            function refresh() {
                var q = normalize(input.value);
                var found = 0;
                items.forEach(function (card) {
                    var categories = (card.dataset.iagrlCategories || '').split(' ');
                    var matches = (filter === 'all' || categories.indexOf(filter) !== -1) && normalize(card.dataset.iagrlText).indexOf(q) !== -1;
                    if (matches) found += 1;
                    card.hidden = !matches || found > shown;
                });
                result.textContent = found + (found === 1 ? ' resource' : ' resources');
                empty.hidden = found !== 0;
                more.hidden = found <= shown;
            }
            chips.forEach(function (chip) {
                chip.addEventListener('click', function () {
                    filter = chip.dataset.iagrlFilter;
                    shown = limit;
                    chips.forEach(function (button) {
                        var active = button === chip;
                        button.classList.toggle('is-active', active);
                        button.setAttribute('aria-pressed', active ? 'true' : 'false');
                    });
                    refresh();
                });
            });
            input.addEventListener('input', function () { shown = limit; refresh(); });
            more.addEventListener('click', function () { shown += limit; refresh(); });
            if (reset) reset.addEventListener('click', function () {
                input.value = '';
                chips[0].click();
                input.focus();
            });
            refresh();
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
    else start();
    // Elementor sometimes inserts shortcode contents after the initial page load.
    document.addEventListener('elementor/frontend/init', start);
})();
