# wheelmaker.dev — Laravel 13 upgrade + redesign + Hetzner deploy

## Status — LIVE on Hetzner, Nov 11 2026 (evening)
- **Live:** https://wheelmaker.dev/ — Laravel 13.42.0, PHP 8.3.6, Caddy 2.11.7 +
  php-fpm on Hetzner consolidated-1 (46.225.138.74), `/srv/wheelmaker`.
  DNS: Route 53 apex + www A records → 46.225.138.74 (TTL 300), cut over
  Nov 11 ~19:50 CT from the old CloudFront/Vapor aliases. Let's Encrypt cert
  auto-provisioned by Caddy. www 301s to the apex.
- **Vapor is dead:** Matt said Nov 11 he no longer has Vapor. The vapor.yml,
  laravel/vapor-core and flysystem-s3 are removed. Staging moved to Hetzner
  the same evening (separate copy at /srv/wheelmaker-staging, own .env and
  Caddy vhost, DNS A → 46.225.138.74). Nothing left on CloudFront/Vapor.
- **De-personalized Nov 11 (evening, 1c6218e):** Matt's call — no location
  anywhere, no family/personal details, no framework branding. Footer no
  longer says "Built with Laravel"; the XSRF-TOKEN cookie is suppressed
  (custom VerifyCsrfToken middleware, `$addHttpCookie = false` — forms read
  the token from the meta tag); session cookie is `wheelmaker-session`
  (APP_NAME=wheelmaker). Custom 404 in the site design. Hero is now
  "I build AI products at Meta."
- **Redesign (same branch):** the Vue 2/Vuetify SPA, laravel-mix, webpack and
  package.json are gone. The site is one Blade layout + home view, hand-written
  CSS (`public/css/site.css`), vanilla JS (`public/js/site.js`) — paper +
  ink + one terracotta accent, Fraunces/Inter. No framework, no trackers, no
  build step. UA analytics tag dropped. `noindex` meta kept (as before).
- **No database:** the /api/user endpoint, TokenGuard, User model, auth config,
  migrations, factories and seeders are all removed. File sessions/cache, sync
  queue. The CVE surface goes away with the endpoint.
- **Contact form:** validation, honeypot (fake 200 on fill) + throttle 5/min,
  reCAPTCHA v3 when GOOGLE_RECAPTCHA_SECRET is set (skipped otherwise — the
  secret was lost with the Vapor env; site key in config/app.php). Mail via
  Amazon SES SMTP (us-east-1, domain verified): IAM user
  `wheelmaker-ses-smtp`, send-only policy, SMTP password derived with
  **VERSION 0x04** (the old 0x02 derivation gets 535 — AWS changed it).
  Verified end-to-end live: form POST → 200, mail delivered to
  matt.t.rademacher@gmail.com.
- **Resume download:** POST /download-resume, same bot check, 302 to
  MattRademacher.pdf. **The PDF is STALE** (Gartner, Feb 2020–Present) —
  Matt needs to supply a current résumé.
- **Deploy record:** branch `upgrade/laravel-12` (name predates the 13
  decision), merged to main Nov 11. Box clone tracks main; deploy = pull +
  `composer install --no-dev` + `config:cache` + `route:cache` + `view:cache`
  + reload php-fpm if needed. `.env` on the box only (600, root-owned);
  APP_KEY generated fresh for production Nov 11. NOTE: the /srv/wheelmaker
  clone was single-branch (refspec only upgrade/laravel-12), so plain
  `git pull` silently never updated master — fixed Nov 11 by setting the
  refspec to `+refs/heads/*:refs/remotes/origin/*` and tracking
  origin/master. If a deploy ever says "Already up to date" at an old
  commit, check the refspec first.
- **Gotchas found live:** (1) Laravel 11+ mail scheme must be `smtp`/`smtps`
  — `tls` throws UnsupportedSchemeException. (2) SES SMTP password VERSION
  is 0x04 now, not 0x02. (3) An SES send policy scoped to the domain identity
  ARN is denied over SMTP (554) — the send-only policy needs Resource "*".
  (4) This VM's egress proxy caches DNS for wheelmaker.dev — verify the live
  site with `curl --resolve wheelmaker.dev:443:46.225.138.74`.

## What the app is (now)
- `GET /` → home view (editorial one-pager: Now / Work / About / Contact)
- `POST /contact` → ContactController (throttle 5,1): validate, honeypot,
  reCAPTCHA, `Mail::raw` to matt.t.rademacher@gmail.com from
  noreply@wheelmaker.dev, subject "personal site inquiry", JSON response
- `POST /download-resume` → DownloadController (throttle 10,1): bot check,
  redirect to asset('MattRademacher.pdf')
- `GET /up` → health check. No API routes, no auth, no DB.

## History (Nov 11 daytime — superseded by the redesign above)
- CVE-2026-114509 / GHSA-f9x3-7vq9-cph6 (CVSS 9.2, TokenGuard auth bypass)
  via Dependabot alert #351 on the Laravel 6.0.3 pin. Matt chose the upgrade.
- Fresh Laravel 13.42.0 skeleton + port (not stepwise 6→12; app is tiny).
  Mix build was fixed (webpack pinned exactly 5.88.2 — 5.90+ removed
  `SizeFormatHelpers` mix 6.0.49 needs; vue-loader ^15.10.0; `.vue()` in
  webpack.mix.js) and 4 TokenGuard regression tests were written — all
  discarded in the evening redesign when Vue and /api/user were removed.
- Found during verification: unauthenticated /api/user returned 500 ("Route
  [login] not defined") — pre-existing in the Laravel 6 app too. Moot now
  (endpoint removed).

## Open items for Matt
- Current résumé PDF (the live one says Gartner, Feb 2020–Present — and it
  still lists Laravel as a skill, so the new one is also the moment that
  signal goes away).
- reCAPTCHA v3 secret (optional — the form works without it via honeypot +
  throttle; adding the secret turns the Google check on).
- Whether the site should stay `noindex` or be indexed.
