# wheelmaker.dev — Laravel 6.0.3 → 13.x upgrade plan

## Status — built + verified locally, Nov 11 2026 (NOT yet deployed)
- Fresh Laravel 13.42.0 skeleton + port, branch `upgrade/laravel-12` (sic — the
  branch name predates the 13 decision; the code is Laravel 13).
- `composer update` clean, no vulnerability advisories. `php artisan test`: 6 passed
  (incl. 4 TokenGuard regression tests — non-string api_token → 401).
- Live serve test (sqlite): GET / → 200; GET /api/user → 401 JSON (plain + JSON
  accept); GET /api/user?api_token[]=x → 401 JSON. POST /contact → 419 without
  CSRF token (correct).
- Mix build fixed: webpack pinned to exactly 5.88.2 (5.90+ removed
  `SizeFormatHelpers` that mix 6.0.49 needs), vue-loader ^15.10.0 added,
  webpack.mix.js gains `.vue()`. `npx mix --production` compiles clean;
  yarn.lock regenerated (webpack 5.88.2, mix 6.0.49, vue-loader 15.11.1).
  Yarn 1.x needs registry https://registry.npmjs.org (registry.yarnpkg.com
  stalls behind this proxy) — set in ~/.yarnrc, NOT in the repo.
- Found during verification: unauthenticated /api/user returned 500 ("Route
  [login] not defined") because no login route exists — pre-existing in the
  Laravel 6 app too, not a regression. Fixed via redirectGuestsTo in
  bootstrap/app.php (api/* → null = 401 JSON; web → '/').
- STILL NEEDED: Vapor API token from Matt (or he runs the deploy himself).
  Deploy order: staging first, verify, then production. Vapor env vars to
  check at deploy: MAIL_MAILER (was MAIL_DRIVER), GOOGLE_RECAPTCHA_SECRET,
  DB_* unchanged, APP_KEY unchanged.

## Decision
Fresh Laravel 13 skeleton + port. The app is tiny (home page, contact form,
resume download, one API route). Stepwise 6→7→8→9→10→11→12 would touch every
config file anyway and take far longer.

## What the app actually is
- `GET /` → home blade (Vue 2 + Vuetify 2 SPA shell, `mix('js/app.js')`)
- `POST /contact` → ContactController: reCAPTCHA v3 check (score > .3) then
  `Mail::raw` to matt.t.rademacher@gmail.com, from noreply@wheelmaker.dev
- `POST /download-resume` → DownloadController: reCAPTCHA v3 check then
  redirect to `asset('MattRademacher.pdf')` (S3 stream code commented out)
- `GET /api/user` (`auth:api`, TokenGuard) → returns authenticated user
- User model only; migrations: users, password_resets, failed_jobs (stock)
- All providers stock/empty; Handler stock; Console Kernel stock; no custom
  commands; no factories used by seeds (seeder empty); no auth routes in
  web.php — Auth controllers are dead code, do NOT port them
- reCAPTCHA secret: `config('app.recaptcha_secret')` = env GOOGLE_RECAPTCHA_SECRET
- Frontend: Vue 2.6 + Vuetify 2 + laravel-mix 4 + webpack. STAYS as-is except
  mix 4 → 6 (webpack 5, works on modern Node). No Vuetify 3 rewrite — out of
  scope, zero security benefit. Compiled assets are committed (public/js/app.js,
  public/css/app.css, mix-manifest.json).

## Target versions
- PHP 8.3 (Vapor runtime `php-8.3`), Laravel 13.x (13.42.0 installed — well past
  the patched 13.29.0 and Dependabot's 12.61.1 floor)
- laravel/vapor-core ^2.0 (v2.47.1 — there is no v3; it supports illuminate ^13.0),
  guzzlehttp/guzzle ^7.9, league/flysystem-aws-s3-v3 ^3.0
- laravel/tinker ^3.0, fakerphp/faker ^1.23, phpunit/phpunit ^12.5,
  laravel/pint ^1.27, mockery/mockery ^1.6, nunomaduro/collision ^8.6
- Drop: fideloper/proxy (in framework since 9), facade/ignition (→ spatie/laravel-ignition
  via framework), fzaninotto/faker (abandoned → fakerphp/faker)

## Port checklist (skeleton = `composer create-project laravel/laravel`)
- [ ] composer.json — rewrite requires per above
- [ ] bootstrap/app.php — new style: routing (web+api), trustProxies('*') for Vapor,
      redirectGuestsTo: api/* → null (401 JSON, no login route exists) else '/',

      exceptions default
- [ ] routes/web.php — FQCN array syntax `[ContactController::class, 'post']`;
      routes/api.php — same for /user; routes/console.php — keep inspire or drop
- [ ] app/Models/User.php — move from App\User, namespace App\Models; $fillable,
      $hidden, $casts same (casts: email_verified_at datetime; add
      'password' => 'hashed' per 12 convention — careful: existing hashes are
      bcrypt, 'hashed' cast only hashes on set, compatible)
- [ ] app/Http/Controllers/{Controller,ContactController,DownloadController}.php —
      port as-is (namespace App\Http\Controllers; ContactController's unused
      Guzzle imports: drop)
- [ ] database/migrations — KEEP the 3 existing migrations verbatim; DELETE the
      skeleton's 0001_01_01_000000_create_users_table / password_reset_tokens /
      sessions / 0001_01_01_000002_create_jobs_table (live DB already has the
      old tables; Vapor deploy runs no migrations anyway)
- [ ] database/factories/UserFactory.php — rewrite class-based, namespace
      Database\Factories, model App\Models\User
- [ ] database/seeders/DatabaseSeeder.php — namespace Database\Seeders, keep empty
- [ ] config/app.php — slim 12 style + 'recaptcha_secret' key
- [ ] config/auth.php — keep api token guard + users provider, model App\Models\User
- [ ] config/database.php, config/mail.php, config/filesystems.php, config/queue.php,
      config/session.php, config/cache.php, config/logging.php, config/services.php,
      config/broadcasting.php, config/view.php, config/cors.php — 12 defaults,
      mail 'default' => env('MAIL_MAILER', env('MAIL_DRIVER', 'smtp')) for
      backward compat with Vapor env
- [ ] DROP: app/Http/Kernel.php, app/Console/Kernel.php, app/Exceptions/Handler.php,
      app/Providers/*, app/Http/Middleware/* (framework defaults cover them;
      TrustProxies handled in bootstrap/app.php), server.php, resources/lang,
      Auth controllers
- [ ] public/index.php, artisan — from skeleton
- [ ] .env.example — update MAIL_DRIVER → MAIL_MAILER (+ keep note), add
      GOOGLE_RECAPTCHA_SECRET, Vapor vars
- [ ] vapor.yml — add `runtime: php-8.3` to both envs; build steps unchanged
      (composer install --no-dev, event:cache, yarn prod)
- [ ] package.json — laravel-mix ^6.0.49, keep vue ^2.6 / vuetify ^2.0 / sass /
      sass-loader ^12; webpack.mix.js unchanged
- [ ] phpunit.xml — 12 format; tests/Examples ported (TestCase + CreatesApplication
      trait — same shape, check bootstrap path)
- [ ] .gitignore — 12 version (compiled mix assets stay tracked)

## Verify locally (needs PHP 8.3 + Composer on this VM)
- [ ] composer install clean, no platform errors
- [ ] `php artisan --version` boots; `php artisan route:list` shows 4 routes
- [ ] phpunit passes (2 example tests)
- [ ] `php artisan serve` + sqlite: GET / returns 200, POST /contact with bad
      captcha fails gracefully, GET /api/user without token → 401
- [ ] TokenGuard regression check: non-string api_token (array) must NOT
      authenticate (the CVE behavior) — expect 401/exception, never a user

## Deploy (needs Matt: Vapor API token via Secure Vault, or he runs it)
- [ ] `vapor deploy staging` → verify staging.wheelmaker.dev 200, routes work
- [ ] `vapor deploy production` → verify wheelmaker.dev 200, contact form,
      resume download, /api/user 401 without token
- [ ] Confirm Dependabot alert #351 clears (and the other 14)
- [ ] Watch: Vapor env vars that may need updating for 12 — MAIL_MAILER (was
      MAIL_DRIVER), DB_* unchanged, GOOGLE_RECAPTCHA_SECRET unchanged,
      APP_KEY unchanged, Vapor's own AWS_* vars unchanged

## Open questions / risks
- Live users table: repo migrations have NO api_token column, so the vulnerable
  /api/user route may already 500 in prod — real-world exposure likely lower
  than the alert sounds. Cannot verify without DB access; patch anyway.
- Vapor build Node version vs laravel-mix 6 + Vue 2: mix 6 / webpack 5 is fine
  on Node 18/20/24; verify in the staging deploy log.
- The `window.vaporAssets.logoImage` references /logo.svg which does not exist
  in public/ — pre-existing, not a regression; leave.
- UA-154495712-1 Google Analytics tag in home.blade.php is ancient (UA is dead);
  leave as-is, not our call to remove.
