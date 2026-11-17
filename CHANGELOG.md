# Changelog

## 1.1.0 (2026-11-18)

- New: `Sendio::transactional()` on the facade, for transactional emails (create, list, get, update, remove, test and stats), and a README example that creates one and sends it with `transactional_id`.
- Needs `sendio/sendio-php` 1.1.0 or newer (the requirement is now `^1.1`), which adds the transactional emails, the `transactional_id` send and the broadcast template link.

## 1.0.0 (2026-11-11)

First public release.
