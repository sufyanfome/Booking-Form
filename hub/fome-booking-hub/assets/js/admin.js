/* Fome Booking Hub — admin JS (vanilla) */
'use strict';

(function () {

/* -------------------------------------------------------------------------
   Colour picker for brand colour field
   ------------------------------------------------------------------------- */
document.querySelectorAll('.fome-colour-preview').forEach(preview => {
	const input = document.getElementById(preview.dataset.for);
	if (!input) return;

	// Sync preview on load
	preview.style.background = input.value || '#4578b4';

	// Open native colour picker
	preview.addEventListener('click', () => input.click());
	input.addEventListener('input', () => {
		preview.style.background = input.value;
	});
});

/* -------------------------------------------------------------------------
   Confirm-before-delete links
   ------------------------------------------------------------------------- */
document.querySelectorAll('[data-confirm]').forEach(el => {
	el.addEventListener('click', e => {
		if (!confirm(el.dataset.confirm || 'Are you sure?')) {
			e.preventDefault();
		}
	});
});

/* -------------------------------------------------------------------------
   Regenerate API key — show one-time copy panel
   ------------------------------------------------------------------------- */
const regenForm = document.getElementById('fome-regen-key-form');
if (regenForm) {
	regenForm.addEventListener('submit', () => {
		const btn = regenForm.querySelector('button[type=submit]');
		if (btn) btn.disabled = true;
	});
}

/* -------------------------------------------------------------------------
   Bookings CSV export form auto-submit on filter change (optional QoL)
   ------------------------------------------------------------------------- */
const filterForm = document.getElementById('fome-bookings-filter');
if (filterForm) {
	filterForm.querySelectorAll('select').forEach(sel => {
		sel.addEventListener('change', () => filterForm.submit());
	});
}

/* -------------------------------------------------------------------------
   Copy-to-clipboard for API key display
   ------------------------------------------------------------------------- */
document.querySelectorAll('.fome-copy-btn').forEach(btn => {
	btn.addEventListener('click', () => {
		const target = document.getElementById(btn.dataset.copy);
		if (!target) return;
		navigator.clipboard.writeText(target.textContent.trim()).then(() => {
			const original = btn.textContent;
			btn.textContent = 'Copied!';
			setTimeout(() => { btn.textContent = original; }, 2000);
		});
	});
});

})();
