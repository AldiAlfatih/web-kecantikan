/**
 * wilayah.js
 * Dropdown wilayah Indonesia bertingkat: Provinsi → Kota/Kab → Kecamatan → Kelurahan
 * API: https://www.emsifa.com/api-wilayah-indonesia
 *
 * Usage:
 *   WilayahDropdown.init({
 *     provinceEl:  document.getElementById('f_province'),
 *     cityEl:      document.getElementById('f_city'),
 *     districtEl:  document.getElementById('f_district'),
 *     villageEl:   document.getElementById('f_village'),
 *     postalEl:    document.getElementById('f_postal'),    // optional
 *     defaults: { province:'11', city:'1101', district:'110101', village:'1101012001' }
 *   });
 */

const WilayahDropdown = (() => {
    const BASE = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    /* ── fetch helper ── */
    async function fetchData(url) {
        try {
            const r = await fetch(url);
            if (!r.ok) throw new Error('Network error');
            return await r.json();
        } catch (e) {
            console.warn('[Wilayah] fetch failed:', url, e);
            return [];
        }
    }

    /* ── populate select ── */
    function populate(sel, items, valueKey, labelKey, placeholder) {
        // keep current value before reset
        const prev = sel.dataset.pendingValue || '';

        sel.innerHTML = `<option value="">${placeholder}</option>`;
        items.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item[valueKey];
            opt.textContent = toTitleCase(item[labelKey]);
            if (item[valueKey] === prev) opt.selected = true;
            sel.appendChild(opt);
        });

        sel.disabled = items.length === 0;
        if (items.length > 0) triggerCustom(sel);

        // Clean pending
        delete sel.dataset.pendingValue;
    }

    function toTitleCase(str) {
        return str.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
    }

    function reset(sel, placeholder) {
        sel.innerHTML = `<option value="">${placeholder}</option>`;
        sel.disabled = true;
        sel.dispatchEvent(new Event('wilayah:populated', { bubbles: true }));
    }

    function triggerCustom(el) {
        el.dispatchEvent(new Event('wilayah:populated', { bubbles: true }));
    }

    /* ── INIT ── */
    function init({ provinceEl, cityEl, districtEl, villageEl, postalEl, defaults = {} }) {
        if (!provinceEl) return;

        // ── Load Provinces ──
        (async () => {
            const data = await fetchData(`${BASE}/provinces.json`);
            provinceEl.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
            data.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = toTitleCase(p.name);
                if (p.id === defaults.province) opt.selected = true;
                provinceEl.appendChild(opt);
            });
            provinceEl.disabled = false;
            triggerCustom(provinceEl);

            // If default province, load cities
            if (defaults.province) {
                cityEl.dataset.pendingValue = defaults.city || '';
                await loadCities(defaults.province);
            }
        })();

        // ── Province → City ──
        provinceEl.addEventListener('change', async () => {
            reset(cityEl,     '-- Pilih Kota/Kabupaten --');
            reset(districtEl, '-- Pilih Kecamatan --');
            reset(villageEl,  '-- Pilih Kelurahan/Desa --');
            if (postalEl) postalEl.value = '';
            if (!provinceEl.value) return;
            await loadCities(provinceEl.value);
        });

        // ── City → District ──
        cityEl.addEventListener('change', async () => {
            reset(districtEl, '-- Pilih Kecamatan --');
            reset(villageEl,  '-- Pilih Kelurahan/Desa --');
            if (postalEl) postalEl.value = '';
            if (!cityEl.value) return;
            await loadDistricts(cityEl.value);
        });

        // ── District → Village ──
        districtEl.addEventListener('change', async () => {
            reset(villageEl, '-- Pilih Kelurahan/Desa --');
            if (postalEl) postalEl.value = '';
            if (!districtEl.value) return;
            await loadVillages(districtEl.value);
        });

        // ── Village → Postal (if postalEl provided) ──
        if (postalEl) {
            villageEl.addEventListener('change', () => {
                // Postal code is not in the API; user must input manually
                // but we can store village name in a hidden field
            });
        }

        /* helpers */
        async function loadCities(provinceId) {
            const data = await fetchData(`${BASE}/regencies/${provinceId}.json`);
            populate(cityEl, data, 'id', 'name', '-- Pilih Kota/Kabupaten --');
            if (defaults.city && !cityEl.dataset.pendingValue) {
                cityEl.dataset.pendingValue = defaults.city;
            }
            // If pending city is now loaded, trigger districts
            const selectedCity = cityEl.value;
            if (selectedCity && defaults.district) {
                districtEl.dataset.pendingValue = defaults.district || '';
                await loadDistricts(selectedCity);
            }
        }

        async function loadDistricts(cityId) {
            const data = await fetchData(`${BASE}/districts/${cityId}.json`);
            populate(districtEl, data, 'id', 'name', '-- Pilih Kecamatan --');
            const selectedDistrict = districtEl.value;
            if (selectedDistrict && defaults.village) {
                villageEl.dataset.pendingValue = defaults.village || '';
                await loadVillages(selectedDistrict);
            }
        }

        async function loadVillages(districtId) {
            const data = await fetchData(`${BASE}/villages/${districtId}.json`);
            populate(villageEl, data, 'id', 'name', '-- Pilih Kelurahan/Desa --');
        }
    }

    return { init };
})();

/* ===================================================
   CUSTOM SELECT — Elegant Searchable Dropdown
   Wrap any <select class="custom-sel"> automatically
=================================================== */
(function () {
    function buildCustomSelect(nativeSelect) {
        if (nativeSelect.dataset.customized) return;
        nativeSelect.dataset.customized = '1';
        nativeSelect.style.display = 'none';

        /* wrapper */
        const wrapper = document.createElement('div');
        wrapper.className = 'csel-wrapper';
        nativeSelect.parentNode.insertBefore(wrapper, nativeSelect);
        wrapper.appendChild(nativeSelect);

        /* trigger */
        const trigger = document.createElement('div');
        trigger.className = 'csel-trigger';
        trigger.setAttribute('tabindex', '0');
        wrapper.insertBefore(trigger, nativeSelect);

        const triggerText = document.createElement('span');
        triggerText.className = 'csel-trigger-text';
        const triggerArrow = document.createElement('span');
        triggerArrow.className = 'csel-arrow';
        triggerArrow.innerHTML = '&#8964;';
        trigger.appendChild(triggerText);
        trigger.appendChild(triggerArrow);

        /* dropdown */
        const dropdown = document.createElement('div');
        dropdown.className = 'csel-dropdown';

        /* search */
        const searchWrap = document.createElement('div');
        searchWrap.className = 'csel-search-wrap';
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.className = 'csel-search';
        searchInput.placeholder = 'Cari...';
        searchWrap.appendChild(searchInput);
        dropdown.appendChild(searchWrap);

        /* list */
        const list = document.createElement('ul');
        list.className = 'csel-list';
        dropdown.appendChild(list);

        wrapper.appendChild(dropdown);

        /* ── sync from native select ── */
        function sync() {
            list.innerHTML = '';
            const opts = Array.from(nativeSelect.options);
            opts.forEach(opt => {
                const li = document.createElement('li');
                li.className = 'csel-item' + (opt.selected ? ' selected' : '') + (opt.value === '' ? ' placeholder' : '');
                li.textContent = opt.textContent;
                li.dataset.value = opt.value;
                li.addEventListener('click', () => {
                    nativeSelect.value = opt.value;
                    nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    syncTrigger();
                    close();
                });
                list.appendChild(li);
            });
            syncTrigger();
            filterList('');
            searchInput.value = '';

            // disable/enable trigger
            if (nativeSelect.disabled) {
                wrapper.classList.add('csel-disabled');
                trigger.setAttribute('tabindex', '-1');
            } else {
                wrapper.classList.remove('csel-disabled');
                trigger.setAttribute('tabindex', '0');
            }
        }

        function syncTrigger() {
            const sel = nativeSelect.options[nativeSelect.selectedIndex];
            triggerText.textContent = sel ? sel.textContent : '';
            triggerText.classList.toggle('csel-placeholder', !sel || sel.value === '');
        }

        function filterList(q) {
            const lq = q.toLowerCase();
            Array.from(list.children).forEach(li => {
                li.style.display = li.textContent.toLowerCase().includes(lq) ? '' : 'none';
            });
        }

        /* ── open / close ── */
        let isOpen = false;
        function open() {
            if (nativeSelect.disabled) return;
            isOpen = true;
            dropdown.classList.add('open');
            wrapper.classList.add('csel-open');
            searchInput.value = '';
            filterList('');
            searchInput.focus();
            // Scroll selected into view
            const sel = list.querySelector('.selected');
            if (sel) sel.scrollIntoView({ block: 'nearest' });
        }
        function close() {
            isOpen = false;
            dropdown.classList.remove('open');
            wrapper.classList.remove('csel-open');
        }

        trigger.addEventListener('click', () => isOpen ? close() : open());
        trigger.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); isOpen ? close() : open(); }
            if (e.key === 'Escape') close();
        });

        searchInput.addEventListener('input', () => filterList(searchInput.value));
        searchInput.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });

        document.addEventListener('click', e => {
            if (!wrapper.contains(e.target)) close();
        });

        /* ── observe native select changes (wilayah API updates) ── */
        nativeSelect.addEventListener('wilayah:populated', sync);
        nativeSelect.addEventListener('change', () => {
            // highlight selected
            Array.from(list.children).forEach(li => {
                li.classList.toggle('selected', li.dataset.value === nativeSelect.value);
            });
            syncTrigger();
        });

        // MutationObserver for DOM changes (options added/removed)
        const obs = new MutationObserver(sync);
        obs.observe(nativeSelect, { childList: true, attributes: true, attributeFilter: ['disabled'] });

        sync();
    }

    /* init all .custom-sel on DOM ready */
    function initAll() {
        document.querySelectorAll('select.custom-sel').forEach(buildCustomSelect);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // expose for dynamic elements
    window.CustomSelect = { build: buildCustomSelect, initAll };
})();
