# StudyGrove

A small Q&A web application where students post questions about their
coursework and help each other — built for COMP1841 Web Programming 1.

**Stack:** PHP 8 (PDO only, no MySQLi) · MySQL/MariaDB · custom MVC
(router → controllers → repositories → views) following the structure
shown in class · **Bootstrap 5.3** CSS framework with a small custom
theme layer · PHPMailer over SMTP.

## Quick start (XAMPP)

1. Copy the `web_programing` folder into `htdocs`.
2. Start Apache + MySQL, then import `database/web_programing.sql`
   in phpMyAdmin (it creates the `web_programing` database, tables and
   seed data).
3. Open **http://localhost/web_programing**

Optional: `public/check.php` verifies the environment (PHP version,
extensions, DB, permissions) and explains how to fix anything that fails.

## Test accounts

| Role   | Login (email or username)           | Password     |
| ------ | ----------------------------------- | ------------ |
| Admin  | `admin123` / admin@studygrove.local | `admin123`   |
| Member | `thao.nguyen`                       | `Member@123` |
| Member | `minh.tran`                         | `Member@123` |
| Member | `ha.le`                             | `Member@123` |

The admin additionally sees **Modules**, **Users** and **Inbox** in the
navigation.

## Features

**Core requirements** — public question list with author/module/image;
create, edit and delete questions (with screenshot upload); admin CRUD
for users and for modules; questions assigned to a user and a module via
dropdowns / session; contact form that stores the message and e-mails
the administrator with PHPMailer over SMTP; when SMTP is not
configured the message is still saved and listed in the admin inbox,
so the feature works offline.

**Additional functionality** — registration and login (bcrypt-hashed
passwords, login by email _or_ username), member/admin roles, keyword search
and module filter with pagination, per-question ownership rules (only
the author or an admin may edit or delete), server-side _and_ HTML5
client-side validation with per-field error messages, accessibility
(skip link, labels, focus styles, `aria-current` navigation, alt text),
admin message inbox.

**Referential integrity** — `questions.user_id` → `users.id`
(`ON DELETE CASCADE`: content follows its author) and
`questions.module_id` → `modules.id` (`ON DELETE RESTRICT`: a module
that still has questions cannot be deleted; the UI shows a friendly
error instead). Named constraints live in `database/web_programing.sql`.

## Front end (Bootstrap 5.3)

The interface is built with the **Bootstrap 5.3** CSS framework, which the
assignment brief explicitly permits. Bootstrap is loaded from the jsDelivr
CDN in `app/Views/partials/header.php` (CSS) and
`app/Views/partials/footer.php` (the JavaScript bundle, which powers the
collapsing navigation menu on small screens).

Bootstrap supplies the responsive 12-column grid, navbar, cards, tables,
forms, alerts, badges, pagination and buttons. `public/css/style.css` is
now a short **theme layer** loaded after Bootstrap: it re-declares
Bootstrap's own `--bs-*` custom properties in the StudyGrove palette
(cream paper, deep green, gold focus ring) instead of overriding rules
with `!important`, and adds the few small pieces Bootstrap has no
component for — the skip link, the round user avatar, the module chip
and the brand mark.

Because Bootstrap comes from a CDN, the machine needs an internet
connection the first time a page is opened. To run the site completely
offline, download `bootstrap.min.css` and `bootstrap.bundle.min.js` into
`public/css/` and `public/js/`, then point the two tags in the header and
footer at those local files instead.

## Configuration

Everything machine-specific is in `config/config.php`:
`BASE_URL` (change only this line if the folder is renamed), database
credentials (XAMPP defaults), and the optional Gmail SMTP settings

## Attribution

Third-party code: **Bootstrap 5.3.3** (MIT licence, https://getbootstrap.com)
loaded from the jsDelivr CDN, and **PHPMailer 6.9.3** (LGPL 2.1) vendored
under `vendor/phpmailer/`.
