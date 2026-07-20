# FastKlean Booking Platform — Claude Code Guidelines

## NON-NEGOTIABLE: The Email Contract

The booking notification email is parsed automatically by the operations system. The **entire email envelope and body must remain byte-for-byte identical** to the current output. This is the single most important constraint in this project.

### Envelope (frozen — do not change)

- **Subject:** `{Service} Booking` — e.g. `Regular Cleaning Booking`
- **From:** per-site `from_email` / `from_name` as stored in the hub
- **To:** per-site `recipient_emails` list (JSON array in hub DB)
- **Format:** `isHTML(true)`
- **Footer (appended to body, parsed — preserve exactly):**
  ```
  \r\n <br /><br /> This email was sent from the online booking system on {footer_site_name} ({footer_site_url})
  ```

### Body structure (exact order, exact labels, exact markup)

```html
<h2>{Service}</h2>
<b>Name:</b> {name} <br />
<b>Phone:</b> {phone} <br />
<b>Email:</b> {email} <br />
<b>Postcode:</b> {postcode} <br />
<b>Preferred calling hours:</b> {calling_hours} <br />
<b>Address:</b> {address} <br />           ← only if address set
<b>Additional notes:</b> {note or "-"} <br />
<b>Date:</b> {dd/mm/yyyy} <br />
<b>Time:</b> {time} <br />                ← only if time set
<b>Price:</b> &pound;{price} (VAT incl.)<br />
    ↳ discounted: <b>Price:</b> <del>&pound;{original}</del> &pound;{price}  (VAT incl.)<br />
<b>Gift card code:</b> ... <br />         ← conditional
<b>Gift card amount used:</b> ... <br /> ← conditional
<b>Discount code:</b> ... <br />          ← conditional
<b>Parking provided:</b> {value}<br />    ← conditional
<b>Congestion Charge Zone:</b> Yes<br /> ← conditional
<b>Get keys from different address:</b> ...<br /> ← conditional
<br />
{service-specific block}
```

### Quirks — preserve exactly

- Trailing space before `<br />` on common fields: `$name <br />`
- `&pound;` entity (not `£`)
- `(VAT incl.)` wording
- Double space in discounted line: `</del> &pound;{price}  (VAT incl.)`
- `<h2>` for service heading (no attributes)
- `-` for empty notes
- Date in `dd/mm/yyyy` format (transformed from HTML date input)
- Per-service field labels must match `submit.php` exactly — see `legacy/booking-form-new/submit.php`

### Email renderer location

`shared/email-contract/EmailRenderer.php` — single pure function `render_booking_email(array $booking): string`

**Golden-file regression tests** live in `shared/email-contract/tests/`. Any change that alters email output for existing inputs must fail CI.

---

## Architecture summary

- **Hub:** `hub/fome-booking-hub/` — WordPress plugin on `bookings.fastklean.co.uk`
- **Site plugin:** `plugin/fome-booking/` — installed on each client site
- **Shared:** `shared/email-contract/` — email renderer used by the site plugin
- **Legacy:** `legacy/booking-form-new/` — read-only reference; do not modify

## Security rules

- Stripe secret keys and SMTP passwords must never appear in code; they live in hub DB encrypted with libsodium
- Price is always recalculated server-side before Stripe session creation
- WP nonces on all form submissions
- Sanitise all inputs — but sanitisation must not alter rendered email output for legitimate values
