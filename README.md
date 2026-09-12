# Sienna Retail — store inventory, POS & analytics

Vanilla HTML/CSS/JS frontend + plain PHP backend + MySQL. No framework,
no build step, no Composer. Built so it clones onto a completely
different machine and runs with two commands.

Started life as a plain auth boilerplate ("Kiln"), now extended into a
small retail store system: staff accounts, a product/category catalog,
a point-of-sale checkout flow, and a live analytics dashboard — all on
top of a 3NF MySQL schema.

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

## Database schema (3NF)

| Table                 | Purpose                                                        |
| ---------------------- | -------------------------------------------------------------- |
| `users`                | Staff accounts for signing in (not customer data)               |
| `categories`           | Product category classifications                               |
| `products`             | Catalog items — pricing, stock level, reorder threshold         |
| `sales`                | One row per checkout / receipt                                  |
| `transaction_details`  | Line items per sale (junction table, resolves the M:N between `sales` and `products`) |

`database/init.sql` creates all five tables and seeds:
- 3 categories and 5 starter products
- a ready-to-use login: **`admin` / `admin123`**
- 4 demo transactions so the Analytics dashboard has real numbers on first run

## Folder structure

```
web-system-project-bsit/
├── docker-compose.yml       # MySQL (+ phpMyAdmin) containers
├── .env.example             # copy to .env — read by both Docker and PHP
├── database/
│   └── init.sql             # schema + seed data, auto-run on first container boot
├── config/
│   ├── database.php         # .env loader + PDO connection
│   └── session.php          # hardened session bootstrap
└── public/                  # PHP's document root — this is what's served
    ├── index.php            # public landing page (auth-aware CTAs)
    ├── login.php
    ├── register.php
    ├── dashboard.php        # protected: Store Analytics — redirects to login.php if no session
    ├── pos.php              # protected: Point of Sale / checkout
    ├── partials/
    │   ├── header.php       # shared <head> + nav (auth-aware links)
    │   └── footer.php       # shared scripts + </body>
    ├── api/
    │   ├── register.php
    │   ├── login.php
    │   ├── logout.php
    │   ├── me.php           # session check, used by the nav's auth-state toggle
    │   ├── products.php     # list/create products
    │   ├── sales.php        # process a checkout (writes sales + transaction_details, decrements stock)
    │   └── analytics.php    # low stock, fast-movers, top revenue, total revenue
    └── assets/
        ├── css/style.css
        └── js/               # api.js, nav.js, login.js, register.js, pos.js, analytics.js
```

## First-time setup

**Prerequisites:** [Docker Desktop](https://www.docker.com/products/docker-desktop/)
and PHP on your `PATH` (installing XAMPP or Laragon both give you this —
Laragon adds it to PATH automatically; for XAMPP add
`C:\xampp\php` to PATH, or just use Laragon's terminal, which is one
click).

```bash
# 1. clone
git clone <your-repo-url> web-system-project-bsit
cd web-system-project-bsit

# 2. copy the env file (both docker-compose and PHP read this one file)
  cp .env.example .env

# 3. start the database — first run auto-creates the schema + seed data from database/init.sql
docker compose up -d

# 4. start the app (from the project root)
php -S localhost:8000 -t public
```

Open **http://localhost:8000** and sign in with `admin` / `admin123`
(or register a new staff account) to reach the Point of Sale and
Analytics pages.

> **Note:** `init.sql` only runs the *first* time the MySQL container's
> data volume is created. If you've already started the container
> before pulling schema/seed-data changes, run
> `docker compose down -v && docker compose up -d` to wipe the volume
> and re-run it (this deletes any data currently in that database).

Optional: **http://localhost:8080** opens phpMyAdmin if you want to look
at the tables directly (server: `mysql`, user: `root`, password:
whatever you set in `.env`).

## Moving to your friend's laptop

Because the database is a container, not an install, this is genuinely
just:

```bash
git clone <your-repo-url>
cd web-system-project-bsit
cp .env.example .env
docker compose up -d
php -S localhost:8000 -t public
```

No matter what's on that machine already (XAMPP, Laragon, neither,
a totally different MySQL version) — the container is identical every
time, so there's nothing to reconcile.

## API endpoints

| Method | Path                 | Body                        | Notes                                                        |
| ------ | -------------------- | ---------------------------- | ------------------------------------------------------------- |
| POST   | `/api/register.php`  | `username, email, password`  | Hashes password with `password_hash()`                        |
| POST   | `/api/login.php`     | `identifier, password`       | `identifier` = username or email; starts session               |
| POST   | `/api/logout.php`    | –                             | Destroys session                                                |
| GET    | `/api/me.php`        | –                             | Returns current user or `401`                                  |
| GET    | `/api/products.php`  | –                             | List all products with category name (requires session)        |
| POST   | `/api/products.php`  | `category_id, product_name, cost_price, selling_price, stock_quantity, reorder_level` | Add a product (requires session) |
| POST   | `/api/sales.php`     | `cart: [{ product_id, quantity }]` | Processes a checkout: validates stock, inserts `sales` + `transaction_details`, decrements `stock_quantity` (requires session) |
| GET    | `/api/analytics.php` | –                             | Returns total revenue, low-stock alerts, fast-movers, top revenue products (requires session) |

## Notes for production (not needed for local/dev use)

This is set up for local development. Before deploying anywhere public:

- Don't run MySQL as `root` for the app — create a dedicated DB user
  with access only to the app's database.
- Serve over HTTPS and set the session cookie's `secure` flag.
- Add a CSRF token to the login/register forms.
- Add basic rate limiting on `/api/login.php` to slow down brute-force
  attempts.
- Remove or change the seeded `admin` / `admin123` account.
- Set real, unique values in `.env` — never commit it (already in
  `.gitignore`).
