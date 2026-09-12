# Kiln — a self-contained auth system

Vanilla HTML/CSS/JS frontend + plain PHP backend + MySQL. No framework,
no build step, no Composer. Built so it clones onto a completely
different machine and runs with two commands.

## Why it's built this way

Your problem isn't really "which stack" — it's **"this has to work on
whatever laptop I end up on."** Two decisions solve that:

1. **The database runs in Docker, the app doesn't.** MySQL is packaged
   as a container defined in `docker-compose.yml`. Whichever laptop you're
   on, `docker compose up -d` gives you the exact same database, same
   schema, every time — you never install or configure MySQL by hand,
   and it doesn't matter whether the machine had it before.
2. **The PHP app needs nothing installed beyond PHP itself.** Both XAMPP
   and Laragon ship a `php.exe`. Instead of relying on either tool's
   Apache config (which differs between the two and adds setup steps),
   the app runs with PHP's own built-in server — one command, works
   identically under either tool.

So on a new laptop you install two things — Docker Desktop and
PHP (via XAMPP or Laragon, doesn't matter which) — clone the repo, and
you're running.

## Stack

| Layer    | Choice                                       |
| -------- | -------------------------------------------- |
| Frontend | Plain HTML, CSS, JS (`fetch`, no build step) |
| Backend  | Plain PHP 8 (PDO, no framework)              |
| Database | MySQL 8, via Docker                          |
| Sessions | Native PHP sessions (httpOnly cookie)        |

## Folder structure

```
kiln-auth/
├── docker-compose.yml       # MySQL (+ phpMyAdmin) containers
├── .env.example             # copy to .env — read by both Docker and PHP
├── database/
│   └── init.sql             # schema, auto-run on first container boot
├── config/
│   ├── database.php         # .env loader + PDO connection
│   └── session.php          # hardened session bootstrap
└── public/                  # PHP's document root — this is what's served
    ├── index.php            # public landing page
    ├── login.php
    ├── register.php
    ├── dashboard.php        # protected: redirects to login.php if no session
    ├── partials/
    │   ├── header.php       # shared <head> + nav
    │   └── footer.php       # shared scripts + </body>
    ├── api/
    │   ├── register.php
    │   ├── login.php
    │   ├── logout.php
    │   └── me.php           # session check, used by the nav's auth-state toggle
    └── assets/
        ├── css/style.css
        └── js/               # api.js, nav.js, login.js, register.js
```

## First-time setup

**Prerequisites:** [Docker Desktop](https://www.docker.com/products/docker-desktop/)
and PHP on your `PATH` (installing XAMPP or Laragon both give you this —
Laragon adds it to PATH automatically; for XAMPP add
`C:\xampp\php` to PATH, or just use Laragon's terminal, which is one
click).

```bash
# 1. clone
git clone <your-repo-url> kiln-auth
cd kiln-auth

# 2. copy the env file (both docker-compose and PHP read this one file)
  cp .env.example .env

# 3. start the database — first run auto-creates the schema from database/init.sql
docker compose up -d

# 4. start the app (from the project root)
php -S localhost:8000 -t public
```

Open **http://localhost:8000** — register an account, sign in, you'll
land on the dashboard, sign out from the nav.

Optional: **http://localhost:8080** opens phpMyAdmin if you want to look
at the `users` table directly (server: `mysql`, user: `root`, password:
whatever you set in `.env`).

## Moving to your friend's laptop

Because the database is a container, not an install, this is genuinely
just:

```bash
git clone <your-repo-url>
cd kiln-auth
cp .env.example .env
docker compose up -d
cd /d C:\Websites\web-system-project-bsit
php -S localhost:8000 -t public
```

No matter what's on that machine already (XAMPP, Laragon, neither,
a totally different MySQL version) — the container is identical every
time, so there's nothing to reconcile.

## API endpoints

| Method | Path                | Body                        | Notes                                            |
| ------ | ------------------- | --------------------------- | ------------------------------------------------ |
| POST   | `/api/register.php` | `username, email, password` | Hashes password with `password_hash()`           |
| POST   | `/api/login.php`    | `identifier, password`      | `identifier` = username or email; starts session |
| POST   | `/api/logout.php`   | –                           | Destroys session                                 |
| GET    | `/api/me.php`       | –                           | Returns current user or `401`                    |

## Notes for production (not needed for local/dev use)

This is set up for local development. Before deploying anywhere public:

- Don't run MySQL as `root` for the app — create a dedicated DB user
  with access only to the `kiln_auth` database.
- Serve over HTTPS and set the session cookie's `secure` flag.
- Add a CSRF token to the login/register forms.
- Add basic rate limiting on `/api/login.php` to slow down brute-force
  attempts.
- Set real, unique values in `.env` — never commit it (already in
  `.gitignore`).
