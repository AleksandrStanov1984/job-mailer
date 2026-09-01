<div align="center">

# 📬 Job Mailer

### Local Job Application Mailing Tool

Personalized, sequential and controlled job-application email campaigns built with Laravel.

<br>

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-Database-003B57?style=for-the-badge&logo=sqlite&logoColor=white)
![Blade](https://img.shields.io/badge/Blade-Templates-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)

![JavaScript](https://img.shields.io/badge/JavaScript-Vanilla-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![Vite](https://img.shields.io/badge/Vite-Build-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![SMTP](https://img.shields.io/badge/SMTP-Mail-EA4335?style=for-the-badge&logo=gmail&logoColor=white)
![Platform](https://img.shields.io/badge/Platform-Local%20Desktop-0078D4?style=for-the-badge&logo=windows&logoColor=white)

<br>

![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.3-777BB4?logo=php&logoColor=white)
![Laravel Version](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white)
![Database](https://img.shields.io/badge/Storage-SQLite-003B57?logo=sqlite&logoColor=white)
![License](https://img.shields.io/badge/license-Personal%20Use-lightgrey)

</div>

---

## About

**Job Mailer** is a lightweight local Laravel application for sending personalized job applications to multiple companies in a controlled and transparent way.

Instead of sending one mass email with CC/BCC, Job Mailer creates and sends a separate personalized message for every recipient. Recipient data is imported from JSON, the application letter is loaded from a plain-text template, and application documents can be attached directly before starting a campaign.

The application is designed to run locally and keeps mailing history in SQLite.

---

## ✨ Features

- 📥 Import recipients from JSON
- 📝 Plain-text application templates with placeholders
- 👤 Personalized greetings for every recipient
- 📎 Multiple attachments per campaign
- 👀 Preview of the first rendered email before sending
- 📤 Sequential SMTP sending — one recipient per email
- ⏱️ Configurable pause between emails
- 🛡️ Duplicate-send protection
- 🔍 Duplicate detection inside the same JSON file
- 📊 Live recipient statuses
- 🗂️ Mailing campaign history
- 🔎 History search and filtering
- 💾 Local SQLite database
- 🧹 Automatic cleanup of temporary campaign files
- 🚫 No CC/BCC mass mailing
- 🔐 Local-first architecture

---

## 🧰 Technology Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13 / PHP 8.3+ |
| Frontend | Blade / Vanilla JavaScript |
| Build | Vite |
| Database | SQLite |
| Mail | Laravel Mail / SMTP |
| Storage | Laravel Filesystem |
| Testing | Laravel Test / PHPUnit |
| Runtime | Local desktop environment |

---

## 🔄 Workflow

```text
Recipients JSON
      │
      ▼
TXT Application Template
      │
      ▼
Select Attachments
      │
      ▼
Parse & Validate Recipients
      │
      ▼
Render Personalized Emails
      │
      ▼
Preview First Email
      │
      ▼
Duplicate Protection
      │
      ▼
Sequential SMTP Sending
      │
      ▼
SQLite Mailing History
```

### Typical campaign

1. Select a recipients JSON file.
2. Select a `.txt` application template.
3. Select one or more attachments.
4. Review the first generated email.
5. Configure duplicate protection and sending delay.
6. Start the campaign.
7. Job Mailer sends every email sequentially.
8. Delivery results are written to the local mailing history.
9. Temporary campaign files are removed after completion.

---

## 🖥️ Screenshots

Screenshots of the campaign dashboard and mailing history can be added here.

Recommended repository structure:

```text
docs/
└── screenshots/
    ├── campaign-dashboard.png
    └── mailing-history.png
```

Then they can be displayed in this README with:

```html
<img src="docs/screenshots/campaign-dashboard.png" alt="Job Mailer campaign dashboard" width="100%">
```

---

## 📄 Recipients JSON

Job Mailer reads recipients from a JSON array.

### Example

```json
[
  {
    "company": "Example GmbH",
    "email": "bewerbung@example.de",
    "vacancy": "Produktionsmitarbeiter (m/w/d)",
    "contact_name": "Max Mustermann",
    "contact_salutation": "Herr"
  }
]
```

### Supported fields

| Field | Required | Description |
|---|:---:|---|
| `email` | ✅ | Recipient email address |
| `company` | — | Company name |
| `vacancy` | — | Vacancy or target position |
| `contact_name` | — | Contact person's full name |
| `contact_salutation` | — | `Herr` or `Frau` |

The `email` field is required. Other fields are optional and can be used by template placeholders.

---

## 📝 Email Templates

The application letter is stored as a normal `.txt` file.

### Supported placeholders

```text
{{ greeting }}
{{ company }}
{{ vacancy }}
{{ contact_name }}
{{ contact_salutation }}
{{ email }}
```

### Example template

```text
{{ greeting }}

hiermit bewerbe ich mich bei {{ company }} um die Position als {{ vacancy }}.

Im Anhang finden Sie meinen Lebenslauf sowie weitere Unterlagen zu meiner Bewerbung.

Über die Möglichkeit, mich persönlich bei Ihnen vorzustellen, würde ich mich sehr freuen.

Mit freundlichen Grüßen

Max Mustermann
```

Job Mailer performs simple placeholder replacement. User-provided TXT templates are **not executed as Blade templates**.

---

## 👋 Personalized Greetings

The greeting is generated from the available contact information.

Examples:

```text
Sehr geehrter Herr Mustermann,
Sehr geehrte Frau Musterfrau,
Guten Tag Max Mustermann,
Sehr geehrte Damen und Herren,
```

Rules:

- `Herr` → `Sehr geehrter Herr <last name>,`
- `Frau` → `Sehr geehrte Frau <last name>,`
- Contact name without salutation → `Guten Tag <full name>,`
- No contact person → `Sehr geehrte Damen und Herren,`
- Gender is never inferred automatically

---

## 🛡️ Duplicate Protection

Job Mailer protects against accidentally sending the same application repeatedly.

The duplicate protection period is configurable in days.

A recipient is blocked only when the normalized email address has a previous successful:

```text
sent
```

status inside the configured protection period.

This means:

- successful sends can block duplicates
- failed sends do **not** block future campaigns
- skipped or failed attempts can safely be retried through a new campaign
- email addresses are normalized before comparison
- duplicates inside the same JSON file are detected separately

---

## 📊 Recipient Statuses

Each recipient has an individual status:

| Status | Meaning |
|---|---|
| `pending` | Waiting to be sent |
| `sending` | Currently being processed |
| `sent` | Successfully sent |
| `failed` | Sending failed |
| `skipped_recently_sent` | Blocked by duplicate protection |
| `duplicate_in_file` | Duplicate address inside the current JSON |

The interface provides counters and filters for quickly reviewing campaign results.

---

## 📎 Attachments

Multiple application documents can be selected before starting a campaign.

Supported file types include:

```text
.pdf
.doc
.docx
.jpg
.jpeg
.png
```

Selected files are copied into temporary campaign storage while the campaign is active.

After all recipients have reached a final state, the temporary campaign directory is automatically removed.

Mailing history remains stored in SQLite.

---

## 🗂️ Mailing History

Campaign and recipient history is stored locally.

For each recipient Job Mailer can retain:

- company
- email
- vacancy
- contact name
- contact salutation
- rendered subject
- rendered message
- delivery status
- sent timestamp
- failed timestamp
- skipped timestamp
- error message

The history interface supports searching, filtering and reviewing previous sends without keeping a permanent document archive.

---

## ⚙️ Installation

### 1. Clone the repository

```bash
git clone https://github.com/AleksandrStanov1984/job-mailer.git
cd job-mailer
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Create the environment file

Linux / macOS:

```bash
cp .env.example .env
```

Windows:

```text
Copy .env.example to .env
```

Generate the application key:

```bash
php artisan key:generate
```

### 5. Create SQLite database

Create an empty file:

```text
database/database.sqlite
```

Then configure `.env`:

```env
DB_CONNECTION=sqlite
```

### 6. Run migrations

```bash
php artisan migrate
```

### 7. Build frontend assets

```bash
npm run build
```

### 8. Start Job Mailer

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

---

## 📧 SMTP Configuration

Configure your SMTP provider in `.env`.

### Gmail example

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Job Mailer"
```

For Gmail, use an **App Password** rather than the normal Google account password.

> [!IMPORTANT]
> Never commit `.env`, SMTP credentials, App Passwords or other secrets to Git.

---

## 🧪 Development

Start Vite in development mode:

```bash
npm run dev
```

Run the test suite:

```bash
php artisan test
```

Clear Laravel caches:

```bash
php artisan optimize:clear
```

Create a production frontend build:

```bash
npm run build
```

Check Git changes:

```bash
git diff --check
git status
```

---

## 🔐 Privacy & Security

Job Mailer is designed as a **local-first application**.

Recipient lists, campaign history and application metadata remain on the local machine unless an email is explicitly sent through the configured SMTP provider.

Sensitive or temporary data should never be committed to Git, including:

```text
.env
SMTP credentials
App Passwords
temporary campaign files
local test recipient files
personal application documents
```

---

## 🎯 Design Principles

Job Mailer intentionally focuses on controlled job applications rather than bulk marketing.

```text
✓ One recipient per email
✓ Personalized message
✓ Sequential sending
✓ Configurable delays
✓ Duplicate protection
✓ Transparent history
✓ Local data storage
✓ Temporary attachment storage

✗ No mass CC
✗ No mass BCC
✗ No permanent document library
✗ No hidden bulk sending
```

---

## 📁 Project Structure

A simplified overview:

```text
app/
├── Http/
│   ├── Controllers/
│   └── Requests/
├── Mail/
├── Models/
└── Services/

database/
├── migrations/
└── database.sqlite

resources/
├── css/
├── js/
└── views/

storage/
└── app/
    └── private/
        └── campaigns/
```

---

## 📜 License

This project is currently intended for personal use and development.

---

<div align="center">

### Job Mailer

Built with ❤️ using Laravel, PHP, SQLite and Vanilla JavaScript.

</div>
