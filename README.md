# AgriPort 4H System

**AgriPort 4H** is a specialized web platform designed for managing 4-H Club membership records, agricultural resumes, organizational certifications, regional announcements, and system audit logs.

---

## 🛠 System Requirements

Ensure your server or local environment meets the following specifications:

* **PHP Version:** `^8.4.23`
* **Composer:** `^2.9.4`
* **Database:** MySQL / MariaDB
* **Node.js & NPM:** Latest LTS (for asset compilation via Vite)
* **Required PHP Extensions:**
  `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `gd`, `iconv`, `intl`, `json`, `libxml`, `mbstring`, `openssl`, `pdo_mysql`, `session`, `simplexml`, `tokenizer`, `xml`, `zip`

---

## 🚀 Quick Setup Guide

Follow these steps to set up the project locally on Laragon, XAMPP, or a server:

### 1. Clone the Repository
```bash
git clone <your-repository-url> agriport4h
cd agriport4h
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Configure Environment Variables
Copy the `.env.example` file to create your `.env` configuration file:

```bash
cp .env.example .env
```

Configure your local database credentials inside `.env`:
```ini
APP_NAME="AgriPort 4H"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE="Asia/Manila"

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=4h_hub
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run Database Migrations
Run the database migrations to build the initial schema:

```bash
php artisan migrate
```

### 6. Create Symbolic Storage Link
Generate the storage symlink to enable media uploads and public downloads:

```bash
php artisan storage:link
```

---

## 💻 Development Workflow

To compile assets and serve the application locally:

```bash
# Compile and hot-reload frontend assets
npm run dev

# Start local server (optional if using Laragon virtual host)
php artisan serve
```

---

## 🗄️ Migration History

The repository includes the following core migrations:

1. **System & Framework Base:** `cache`, `jobs`, `lsa_levels`, `suffixes`
2. **Database Setup:** `initial_database_setup`
3. **Core Features:**
   - `create_announcements_table` & `add_media_to_announcements_table`
   - `create_audit_logs_table`
   - `add_acceptance_fields_to_users_table`
   - `add_member_id_to_members_table` & `add_verification_to_members_table`
   - `add_region_id_to_announcements_table`
   - `add_certification_path_to_organizations_table`
   - `add_agri_resume_fields_to_members_table`
   - `add_uid_to_members_table`
   - `add_member_id_to_users_table`

---

## 🛡️ License & Maintenance

This project is maintained for the **4-H Club / AgriPort Platform**. Ensure that sensitive configuration parameters in `.env` are never committed to version control.
