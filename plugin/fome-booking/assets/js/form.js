/* global fomeBooking */
'use strict';

(function () {

/* -------------------------------------------------------------------------
   Time slots per service (matches legacy config/service-hours.php)
   Keys are normalised service name slugs.
   ------------------------------------------------------------------------- */
const TIME_SLOTS = {
	'regular-cleaning':                  { mon:['8:00','12:00','15:00'], tue:['8:00','12:00','15:00'], wed:['8:00','12:00','15:00'], thu:['8:00','12:00','15:00'], fri:['8:00','12:00','15:00'], sat:['8:00','12:00','15:00'], sun:['9:00','12:00','15:00'] },
	'regular-cleaning-luxury':           { mon:['8:00','12:00','15:00'], tue:['8:00','12:00','15:00'], wed:['8:00','12:00','15:00'], thu:['8:00','12:00','15:00'], fri:['8:00','12:00','15:00'], sat:['8:00','12:00','15:00'], sun:['9:00','12:00','15:00'] },
	'commercial-cleaning':               { mon:['8:00','10:00','12:00','14:00','16:00','18:00'], tue:['8:00','10:00','12:00','14:00','16:00','18:00'], wed:['8:00','10:00','12:00','14:00','16:00','18:00'], thu:['8:00','10:00','12:00','14:00','16:00','18:00'], fri:['8:00','10:00','12:00','14:00','16:00','18:00'], sat:['8:00','10:00','12:00','14:00','16:00','18:00'], sun:['9:00','10:00','12:00','14:00','16:00','18:00'] },
	'antiviral-sanitisation':            { mon:['8:00','14:00'], tue:['8:00','14:00'], wed:['8:00','14:00'], thu:['8:00','14:00'], fri:['8:00','14:00'], sat:['8:00','14:00'], sun:['10:00'] },
	'antiviral-sanitisation-commercial': { mon:['8:00','14:00'], tue:['8:00','14:00'], wed:['8:00','14:00'], thu:['8:00','14:00'], fri:['8:00','14:00'], sat:['8:00','14:00'], sun:['10:00'] },
	'one-off-cleaning':                  { mon:['8:00','14:00'], tue:['8:00','14:00'], wed:['8:00','14:00'], thu:['8:00','14:00'], fri:['8:00','14:00'], sat:['8:00','14:00'], sun:['10:00'] },
	'carpet-cleaning':                   { mon:['8:00','10:00','12:00','14:00','16:00'], tue:['8:00','10:00','12:00','14:00','16:00'], wed:['8:00','10:00','12:00','14:00','16:00'], thu:['8:00','10:00','12:00','14:00','16:00'], fri:['8:00','10:00','12:00','14:00','16:00'], sat:['8:00','10:00','12:00','14:00','16:00'], sun:['10:00','12:00','14:00'] },
	'mattress-cleaning':                 { mon:['8:00','10:00','12:00','14:00','16:00'], tue:['8:00','10:00','12:00','14:00','16:00'], wed:['8:00','10:00','12:00','14:00','16:00'], thu:['8:00','10:00','12:00','14:00','16:00'], fri:['8:00','10:00','12:00','14:00','16:00'], sat:['8:00','10:00','12:00','14:00','16:00'], sun:['10:00','12:00','14:00'] },
	'upholstery-cleaning':               { mon:['8:00','10:00','12:00','14:00','16:00'], tue:['8:00','10:00','12:00','14:00','16:00'], wed:['8:00','10:00','12:00','14:00','16:00'], thu:['8:00','10:00','12:00','14:00','16:00'], fri:['8:00','10:00','12:00','14:00','16:00'], sat:['8:00','10:00','12:00','14:00','16:00'], sun:['10:00','12:00','14:00'] },
	'window-cleaning':                   { mon:['8:00','12:00','15:00'], tue:['8:00','12:00','15:00'], wed:['8:00','12:00','15:00'], thu:['8:00','12:00','15:00'], fri:['8:00','12:00','15:00'], sat:['8:00','12:00','15:00'], sun:['9:00','12:00','15:00'] },
	'curtain-cleaning':                  { mon:['8:00','10:00','12:00','14:00','16:00'], tue:['8:00','10:00','12:00','14:00','16:00'], wed:['8:00','10:00','12:00','14:00','16:00'], thu:['8:00','10:00','12:00','14:00','16:00'], fri:['8:00','10:00','12:00','14:00','16:00'], sat:['8:00','10:00','12:00','14:00','16:00'], sun:['10:00','12:00','14:00'] },
	'oven-cleaning':                     { mon:['8:00','10:00','12:00','14:00','16:00'], tue:['8:00','10:00','12:00','14:00','16:00'], wed:['8:00','10:00','12:00','14:00','16:00'], thu:['8:00','10:00','12:00','14:00','16:00'], fri:['8:00','10:00','12:00','14:00','16:00'], sat:['8:00','10:00','12:00','14:00','16:00'], sun:['10:00','12:00','14:00'] },
	'rubbish-removal':                   { mon:['8:00','14:00'], tue:['8:00','14:00'], wed:['8:00','14:00'], thu:['8:00','14:00'], fri:['8:00','14:00'], sat:['8:00','14:00'], sun:['10:00'] },
	'after-builders-cleaning':           { mon:['8:00','14:00'], tue:['8:00','14:00'], wed:['8:00','14:00'], thu:['8:00','14:00'], fri:['8:00','14:00'], sat:['8:00','14:00'], sun:['10:00'] },
	'end-of-tenancy-cleaning':           { mon:['8:00','14:00'], tue:['8:00','14:00'], wed:['8:00','14:00'], thu:['8:00','14:00'], fri:['8:00','14:00'], sat:['8:00','14:00'], sun:['10:00'] },
	'hard-floor-cleaning':               { mon:['8:00','10:00','12:00','14:00','16:00','18:00'], tue:['8:00','10:00','12:00','14:00','16:00','18:00'], wed:['8:00','10:00','12:00','14:00','16:00','18:00'], thu:['8:00','10:00','12:00','14:00','16:00','18:00'], fri:['8:00','10:00','12:00','14:00','16:00','18:00'], sat:['8:00','10:00','12:00','14:00','16:00','18:00'], sun:['9:00','10:00','12:00','14:00','16:00','18:00'] },
	'wooden-floor-polishing':            { mon:['8:00','10:00','12:00','14:00','16:00','18:00'], tue:['8:00','10:00','12:00','14:00','16:00','18:00'], wed:['8:00','10:00','12:00','14:00','16:00','18:00'], thu:['8:00','10:00','12:00','14:00','16:00','18:00'], fri:['8:00','10:00','12:00','14:00','16:00','18:00'], sat:['8:00','10:00','12:00','14:00','16:00','18:00'], sun:['9:00','10:00','12:00','14:00','16:00','18:00'] },
	'gardening':                         { mon:['8:00','12:00','14:00'], tue:['8:00','12:00','14:00'], wed:['8:00','12:00','14:00'], thu:['8:00','12:00','14:00'], fri:['8:00','12:00','14:00'], sat:['8:00','12:00','14:00'], sun:[] },
	'car-valeting':                      { mon:['8:00','10:00','12:00','14:00','16:00'], tue:['8:00','10:00','12:00','14:00','16:00'], wed:['8:00','10:00','12:00','14:00','16:00'], thu:['8:00','10:00','12:00','14:00','16:00'], fri:['8:00','10:00','12:00','14:00','16:00'], sat:['8:00','10:00','12:00','14:00','16:00'], sun:['9:00','10:00','12:00','14:00','16:00'] },
};

const DAY_KEYS = ['sun','mon','tue','wed','thu','fri','sat'];

/* -------------------------------------------------------------------------
   State
   ------------------------------------------------------------------------- */
let state = {
	step: 1,
	service: '',
	serviceSlug: '',
	isCommercial: false,
	discountMultiplier: 1,
	giftCardBalance: 0,
};

/* -------------------------------------------------------------------------
   DOM refs
   ------------------------------------------------------------------------- */
const form       = document.getElementById('fome-booking-form');
if (!form) return; // shortcode not on this page

const steps      = [...form.querySelectorAll('.fome-step')];
const progSteps  = [...document.querySelectorAll('.fome-progress__step')];
const progConns  = [...document.querySelectorAll('.fome-progress__connector')];
const errBox     = document.getElementById('fome-error');
const loadingEl  = document.getElementById('fome-loading');

/* -------------------------------------------------------------------------
   Brand colour injection
   ------------------------------------------------------------------------- */
if (fomeBooking.brandColor) {
	document.querySelector('.fome-booking').style.setProperty('--fome-brand', fomeBooking.brandColor);
}

/* -------------------------------------------------------------------------
   Step navigation
   ------------------------------------------------------------------------- */
function showStep(n) {
	state.step = n;
	steps.forEach(s => {
		const isTarget = parseInt(s.dataset.step, 10) === n;
		s.hidden = !isTarget;
	});
	progSteps.forEach((ps, i) => {
		const sn = i + 1;
		ps.classList.toggle('fome-progress__step--active', sn === n);
		ps.classList.toggle('fome-progress__step--done', sn < n);
	});
	progConns.forEach((c, i) => {
		c.classList.toggle('fome-progress__connector--done', i + 1 < n);
	});
	window.scrollTo({ top: form.getBoundingClientRect().top + window.scrollY - 20, behavior: 'smooth' });
}

/* -------------------------------------------------------------------------
   Service selection
   ------------------------------------------------------------------------- */
document.querySelectorAll('.fome-service-card').forEach(card => {
	card.addEventListener('click', () => {
		document.querySelectorAll('.fome-service-card').forEach(c => c.classList.remove('fome-service-card--selected'));
		card.classList.add('fome-service-card--selected');

		const svcName = card.dataset.service;
		state.service = svcName;
		state.serviceSlug = slugify(svcName);
		state.isCommercial = card.classList.contains('fome-service-card--commercial');

		document.getElementById('fome-service-input').value = svcName;
		showServiceOptions(state.serviceSlug, state.isCommercial);
		document.getElementById('fome-step1-next').disabled = false;
		hideError();
	});
});

function slugify(name) {
	return name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
}

function showServiceOptions(slug, isCommercial) {
	const container = document.getElementById('fome-service-options');
	container.hidden = false;

	// Hide all panels first
	container.querySelectorAll('.fome-options-panel').forEach(p => p.hidden = true);

	// Find matching panel
	let panel = container.querySelector(`.fome-options-panel[data-for="${slug}"]`);
	if (!panel && isCommercial) {
		panel = container.querySelector('.fome-options-panel[data-for="commercial"]');
	}
	if (panel) panel.hidden = false;
}

document.getElementById('fome-step1-next').addEventListener('click', () => {
	if (!state.service) { showError('Please select a service.'); return; }
	showStep(2);
	updateTimeSlots();
});

/* -------------------------------------------------------------------------
   Counter buttons (+/−)
   ------------------------------------------------------------------------- */
document.querySelectorAll('.fome-counter__btn').forEach(btn => {
	btn.addEventListener('click', () => {
		const action = btn.dataset.action;
		const target = btn.dataset.target;
		const input  = form.querySelector(`[id$="${target}"], [name="${target}"]`);
		if (!input) return;
		let val = parseInt(input.value, 10) || 0;
		const min = parseInt(btn.dataset.min ?? 0, 10);
		const max = parseInt(btn.dataset.max ?? 99, 10);
		val = action === 'inc' ? Math.min(val + 1, max) : Math.max(val - 1, min);
		input.value = val;
		input.dispatchEvent(new Event('change', { bubbles: true }));
	});
});

/* -------------------------------------------------------------------------
   Chip groups (single-select)
   ------------------------------------------------------------------------- */
document.querySelectorAll('.fome-chip-group:not(.fome-chip-group--multi)').forEach(group => {
	const hidden = group.nextElementSibling;
	group.querySelectorAll('.fome-chip').forEach(chip => {
		chip.addEventListener('click', () => {
			group.querySelectorAll('.fome-chip').forEach(c => c.classList.remove('fome-chip--selected'));
			chip.classList.add('fome-chip--selected');
			if (hidden && hidden.tagName === 'INPUT') hidden.value = chip.dataset.value;
		});
	});
});

/* -------------------------------------------------------------------------
   Chip groups (multi-select)
   ------------------------------------------------------------------------- */
document.querySelectorAll('.fome-chip-group--multi').forEach(group => {
	const hidden = group.nextElementSibling;
	group.querySelectorAll('.fome-chip').forEach(chip => {
		chip.addEventListener('click', () => {
			chip.classList.toggle('fome-chip--selected');
			if (hidden && hidden.tagName === 'INPUT') {
				const selected = [...group.querySelectorAll('.fome-chip--selected')].map(c => c.dataset.value);
				hidden.value = selected.join(',');
			}
		});
	});
});

/* -------------------------------------------------------------------------
   Pets toggle
   ------------------------------------------------------------------------- */
const havePetsCheck = document.getElementById('fome-have-pets');
const petsDetail    = document.getElementById('fome-pets-detail');
if (havePetsCheck && petsDetail) {
	havePetsCheck.addEventListener('change', () => {
		petsDetail.hidden = !havePetsCheck.checked;
	});
}

/* -------------------------------------------------------------------------
   Date picker — set min to tomorrow
   ------------------------------------------------------------------------- */
const dateInput = document.getElementById('fome-date');
if (dateInput) {
	const tomorrow = new Date();
	tomorrow.setDate(tomorrow.getDate() + 1);
	dateInput.min = tomorrow.toISOString().split('T')[0];
	dateInput.addEventListener('change', updateTimeSlots);
}

function updateTimeSlots() {
	const container = document.getElementById('fome-time-slots');
	const timeInput = document.getElementById('fome-time-input');
	container.innerHTML = '';
	if (timeInput) timeInput.value = '';

	const dateVal = dateInput ? dateInput.value : '';
	if (!dateVal || !state.serviceSlug) return;

	const d       = new Date(dateVal);
	const dayKey  = DAY_KEYS[d.getDay()];
	const slotMap = TIME_SLOTS[state.serviceSlug] || TIME_SLOTS['one-off-cleaning'];
	const slots   = slotMap[dayKey] || [];

	if (!slots.length) {
		container.innerHTML = '<span class="fome-field__note">No slots available on this day for the selected service.</span>';
		return;
	}

	slots.forEach(slot => {
		const btn = document.createElement('button');
		btn.type = 'button';
		btn.className = 'fome-time-slot';
		btn.textContent = slot;
		btn.addEventListener('click', () => {
			container.querySelectorAll('.fome-time-slot').forEach(b => b.classList.remove('fome-time-slot--selected'));
			btn.classList.add('fome-time-slot--selected');
			if (timeInput) timeInput.value = slot;
		});
		container.appendChild(btn);
	});
}

/* -------------------------------------------------------------------------
   Postcode validation (client-side outcode check using config postcodes)
   We only do a basic format check here; server validates against the DB.
   ------------------------------------------------------------------------- */
const postcodeInput = document.getElementById('fome-postcode');
const postcodeStatus = document.querySelector('.fome-postcode-status');
if (postcodeInput) {
	postcodeInput.addEventListener('blur', () => {
		const val = postcodeInput.value.trim().toUpperCase();
		postcodeInput.value = val;
		if (!val) return;
		if (!/^[A-Z]{1,2}\d/.test(val)) {
			setFieldNote(postcodeStatus, 'Please enter a valid UK postcode.', 'error');
		} else {
			setFieldNote(postcodeStatus, '', '');
		}
	});
}

/* -------------------------------------------------------------------------
   Promo code toggle
   ------------------------------------------------------------------------- */
const promoToggle = document.querySelector('.fome-promo__toggle');
const promoFields = document.querySelector('.fome-promo__fields');
if (promoToggle && promoFields) {
	promoToggle.addEventListener('click', () => {
		promoFields.hidden = !promoFields.hidden;
	});
}

/* -------------------------------------------------------------------------
   Discount code apply
   ------------------------------------------------------------------------- */
document.getElementById('fome-apply-discount')?.addEventListener('click', () => {
	const code   = document.getElementById('fome-discount-code')?.value.trim().toUpperCase();
	const status = document.querySelector('.fome-discount-status');
	if (!code) return;

	const match = (fomeBooking.discountCodes || []).find(dc => dc.code === code && dc.status === 'active');
	if (match) {
		state.discountMultiplier = parseFloat(match.multiplier);
		setFieldNote(status, `Code applied: ${Math.round((1 - state.discountMultiplier) * 100)}% off`, 'success');
	} else {
		state.discountMultiplier = 1;
		setFieldNote(status, 'Invalid or expired discount code.', 'error');
	}
	updateLivePriceDisplay();
});

/* -------------------------------------------------------------------------
   Gift card apply
   ------------------------------------------------------------------------- */
document.getElementById('fome-apply-gift')?.addEventListener('click', () => {
	const code   = document.getElementById('fome-gift-card-code')?.value.trim().toUpperCase();
	const status = document.querySelector('.fome-gift-status');
	if (!code) return;

	const match = (fomeBooking.giftCards || []).find(gc => gc.code === code && gc.status === 'active');
	if (match && parseFloat(match.balance) > 0) {
		state.giftCardBalance = parseFloat(match.balance);
		setFieldNote(status, `Gift card applied: £${state.giftCardBalance.toFixed(2)} balance`, 'success');
	} else {
		state.giftCardBalance = 0;
		setFieldNote(status, 'Invalid or empty gift card.', 'error');
	}
	updateLivePriceDisplay();
});

/* -------------------------------------------------------------------------
   Step 2 → 3
   ------------------------------------------------------------------------- */
document.getElementById('fome-step2-next')?.addEventListener('click', () => {
	if (!validateStep2()) return;
	buildReview();
	showStep(3);
});

document.getElementById('fome-step2-back')?.addEventListener('click', () => showStep(1));
document.getElementById('fome-step3-back')?.addEventListener('click', () => showStep(2));

function validateStep2() {
	let ok = true;
	const required = ['name', 'phone', 'email', 'postcode', 'date'];
	required.forEach(name => {
		const el = form.querySelector(`[name="${name}"]`);
		if (!el || !el.value.trim()) {
			ok = false;
			el?.classList.add('fome--invalid');
		} else {
			el?.classList.remove('fome--invalid');
		}
	});

	const timeInput = document.getElementById('fome-time-input');
	if (!timeInput || !timeInput.value) {
		ok = false;
		showError('Please select a time slot.');
	}

	if (!ok && !errBox.textContent) showError('Please fill in all required fields.');
	return ok;
}

/* -------------------------------------------------------------------------
   Review step builder
   ------------------------------------------------------------------------- */
function buildReview() {
	const serviceList  = document.getElementById('fome-review-service');
	const detailsList  = document.getElementById('fome-review-details');
	const totalEl      = document.getElementById('fome-total-price');
	const priceNoteEl  = document.getElementById('fome-price-note');

	// Service section
	const serviceRows = [
		['Service', state.service],
	];
	// Pick up visible service-option values
	const activePanel = document.querySelector(`.fome-options-panel[data-for="${state.serviceSlug}"]:not([hidden])`);
	if (activePanel) {
		activePanel.querySelectorAll('input:not([type=hidden]), select').forEach(inp => {
			if (!inp.name || !inp.value) return;
			const label = activePanel.querySelector(`label[for="${inp.id}"]`)?.textContent
				|| inp.name.replace(/-/g, ' ');
			serviceRows.push([capitalise(label), inp.value]);
		});
		// Chips
		activePanel.querySelectorAll('.fome-chip-group').forEach(group => {
			const selected = [...group.querySelectorAll('.fome-chip--selected')].map(c => c.textContent.trim());
			if (!selected.length) return;
			const hidden = group.nextElementSibling;
			const name = hidden?.name || '';
			if (name) serviceRows.push([capitalise(name.replace(/-/g, ' ')), selected.join(', ')]);
		});
	}
	renderDL(serviceList, serviceRows);

	// Details section
	const detailRows = [
		['Name',    form.querySelector('[name="name"]')?.value],
		['Phone',   form.querySelector('[name="phone"]')?.value],
		['Email',   form.querySelector('[name="email"]')?.value],
		['Postcode',form.querySelector('[name="postcode"]')?.value],
		['Address', form.querySelector('[name="address"]')?.value],
		['Date',    formatDateDisplay(form.querySelector('[name="date"]')?.value)],
		['Time',    document.getElementById('fome-time-input')?.value],
		['Notes',   form.querySelector('[name="note"]')?.value || '-'],
	].filter(([, v]) => v);
	renderDL(detailsList, detailRows);

	// Price
	const price = calculateDisplayPrice();
	if (totalEl) totalEl.textContent = price.display;

	// Submit button label
	const submitBtn = document.getElementById('fome-submit');
	if (state.isCommercial) {
		if (submitBtn) submitBtn.textContent = 'Request free quote →';
		if (totalEl) totalEl.textContent = 'Free quote';
		if (priceNoteEl) priceNoteEl.textContent = 'A member of our team will contact you with pricing.';
	} else {
		if (submitBtn) submitBtn.textContent = 'Confirm & pay →';
		const settings = fomeBooking.settings || {};
		const notes = [];
		if (settings.vat_enabled) notes.push('Includes VAT');
		if (settings.online_discount_multiplier && settings.online_discount_multiplier < 1) {
			notes.push(`${Math.round((1 - settings.online_discount_multiplier) * 100)}% online discount already applied`);
		}
		if (priceNoteEl) priceNoteEl.textContent = notes.join(' · ');
	}
}

function renderDL(dl, rows) {
	dl.innerHTML = '';
	rows.forEach(([label, value]) => {
		if (!value) return;
		const dt = document.createElement('dt');
		dt.textContent = label + ':';
		const dd = document.createElement('dd');
		dd.textContent = value;
		dl.appendChild(dt);
		dl.appendChild(dd);
	});
}

function formatDateDisplay(iso) {
	if (!iso) return '';
	const [y, m, d] = iso.split('-');
	return `${d}/${m}/${y}`;
}

function calculateDisplayPrice() {
	// Client-side estimate (authoritative calculation is server-side)
	// Used only for display in the review step
	const prices   = fomeBooking.prices || {};
	const settings = fomeBooking.settings || {};
	const onlineM  = parseFloat(settings.online_discount_multiplier ?? 1);
	const vatRate  = settings.vat_enabled ? 1.2 : 1;
	const svc      = state.service;

	let net = estimateNet(svc, prices);
	let discounted = net * onlineM;
	let afterDC    = discounted * state.discountMultiplier;
	let withVat    = afterDC * vatRate;
	let final      = Math.max(0, withVat - state.giftCardBalance);

	const vatStr = settings.vat_enabled ? ' (VAT incl.)' : '';
	let display  = `£${final.toFixed(2)}${vatStr}`;

	const hasOriginal = Math.abs(net - final) > 0.01;
	return { display, original: hasOriginal ? `£${net.toFixed(2)}` : '' };
}

function estimateNet(svc, prices) {
	const p = k => parseFloat(prices[k] ?? 0);
	const slug = slugify(svc);

	if (slug.includes('regular-cleaning')) {
		const beds  = parseInt(form.querySelector('[name="bedrooms"]')?.value ?? 1, 10);
		const baths = parseInt(form.querySelector('[name="bathrooms"]')?.value ?? 1, 10);
		const freq  = form.querySelector('[name="frequency"]')?.value || 'weekly';
		const luxury = slug.includes('luxury');
		const key = luxury
			? `luxury_cleaning_${freq}_${beds}bed_${baths}bath`
			: `regular_cleaning_${freq}_${beds}bed_${baths}bath`;
		return p(key) || (parseInt(form.querySelector('[name="hours"]')?.value ?? 2, 10)
			* p(luxury ? 'luxury_hourly_rate' : 'regular_hourly_rate'));
	}
	if (slug === 'one-off-cleaning') {
		const beds  = parseInt(form.querySelector('[name="bedrooms"]')?.value ?? 1, 10);
		const baths = parseInt(form.querySelector('[name="bathrooms"]')?.value ?? 1, 10);
		return p(`one_off_${beds}bed_${baths}bath`);
	}
	if (slug === 'end-of-tenancy-cleaning') {
		const type = form.querySelector('[name="property-type"]')?.value || 'flat';
		const beds = parseInt(form.querySelector('[name="bedrooms"]')?.value ?? 1, 10);
		const furn = form.querySelector('[name="furnished"]')?.value || 'furnished';
		return p(`eot_${type}_${beds}bed_${furn}`);
	}
	if (slug === 'carpet-cleaning') {
		const rooms = parseInt(form.querySelector('[name="carpet-rooms"]')?.value ?? 1, 10);
		return p(`carpet_${rooms}room`);
	}
	if (slug === 'upholstery-cleaning') {
		return p(`upholstery_${form.querySelector('[name="sofa-type"]')?.value || '2seater'}`);
	}
	if (slug === 'mattress-cleaning') {
		return p(`mattress_${form.querySelector('[name="mattress-size"]')?.value || 'single'}`);
	}
	if (slug === 'oven-cleaning') {
		return p(`oven_${form.querySelector('[name="oven-type"]')?.value || 'single'}`);
	}
	if (slug === 'after-builders-cleaning') {
		return p(`after_builders_${form.querySelector('[name="ab-size"]')?.value || 'studio'}`);
	}
	if (slug === 'rubbish-removal') {
		return p(`rubbish_${form.querySelector('[name="rubbish-size"]')?.value || 'small'}`);
	}
	if (slug === 'hard-floor-cleaning') {
		return p(`hard_floor_${form.querySelector('[name="floor-size"]')?.value || 'small'}`);
	}
	if (slug === 'wooden-floor-polishing') {
		return p(`wooden_floor_${form.querySelector('[name="floor-size"]')?.value || 'small'}`);
	}
	if (slug === 'gardening') {
		return p(`gardening_${form.querySelector('[name="garden-size"]')?.value || 'small'}`);
	}
	if (slug === 'car-valeting') {
		return p(`car_valeting_${form.querySelector('[name="car-type"]')?.value || 'hatchback'}`);
	}
	if (slug === 'antiviral-sanitisation') {
		return p(`antiviral_${form.querySelector('[name="antiviral-size"]')?.value || 'studio'}`);
	}
	return 0;
}

function updateLivePriceDisplay() {
	const totalEl = document.getElementById('fome-total-price');
	if (!totalEl) return;
	const { display } = calculateDisplayPrice();
	totalEl.textContent = display;
}

/* -------------------------------------------------------------------------
   Form submission
   ------------------------------------------------------------------------- */
form.addEventListener('submit', async (e) => {
	e.preventDefault();
	if (!validateStep2()) return;

	showLoading(true);
	hideError();

	const data = new FormData(form);
	data.set('action', 'fome_submit_booking');
	// Convert dd/mm/yyyy from date picker
	const rawDate = form.querySelector('[name="date"]')?.value;
	if (rawDate) {
		const [y, m, d] = rawDate.split('-');
		data.set('date', `${d}/${m}/${y}`);
	}

	try {
		const resp = await fetch(fomeBooking.ajaxUrl, {
			method: 'POST',
			body: data,
		});
		const json = await resp.json();
		if (json.success && json.data?.redirect) {
			window.location.href = json.data.redirect;
		} else {
			showError(json.data?.message || 'Something went wrong. Please try again.');
			showLoading(false);
		}
	} catch {
		showError('A network error occurred. Please try again.');
		showLoading(false);
	}
});

/* -------------------------------------------------------------------------
   Helpers
   ------------------------------------------------------------------------- */
function showError(msg) {
	if (!errBox) return;
	errBox.textContent = msg;
	errBox.hidden = false;
}

function hideError() {
	if (!errBox) return;
	errBox.textContent = '';
	errBox.hidden = true;
}

function showLoading(on) {
	if (loadingEl) loadingEl.hidden = !on;
	const submitBtn = document.getElementById('fome-submit');
	if (submitBtn) submitBtn.disabled = on;
}

function setFieldNote(el, msg, type) {
	if (!el) return;
	el.textContent = msg;
	el.className = 'fome-field__note fome-postcode-status';
	if (type) el.classList.add(`fome-field__note--${type}`);
}

function capitalise(str) {
	return str.charAt(0).toUpperCase() + str.slice(1);
}

/* -------------------------------------------------------------------------
   Init
   ------------------------------------------------------------------------- */
showStep(1);

})();
