<?php

/*
 |----------------------------------------------------------------------
 | StudyGrove - system configuration
 |----------------------------------------------------------------------
 | Every setting that changes between machines lives in this one file,
 | so nothing else in the project needs editing when it is moved.
 */

/* ---- 1. Site URLs ----------------------------------------------------
 * The project folder inside htdocs is called "web_programing".
 * If you ever rename that folder, update ONLY the line below.
 */
define('BASE_URL', 'http://localhost/web_programing');
define('PUBLIC_FOLDER', BASE_URL . '/public');

/* ---- 2. Database (XAMPP defaults) ---------------------------------- */
define('DB_HOST', 'localhost');
define('DB_NAME', 'web_programing');
define('DB_USER', 'root');
define('DB_PASS', '');

/* ---- 3. Site identity ----------------------------------------------- */
define('SITE_NAME', 'StudyGrove');
define('ADMIN_EMAIL', 'dangvuong26062006@gmail.com');   // contact form goes here

/* ---- 4. Mail - PHPMailer over SMTP ----------------------------------
 * To send real e-mail through Gmail:
 *   1. turn on 2-Step Verification for the Google account,
 *   2. create an App Password (Google Account > Security > App passwords),
 *   3. put the Gmail address in SMTP_USER and the 16-character
 *      app password in SMTP_PASS.
 * While these are empty the system does NOT break: contact messages are
 * still saved in the database and shown in the admin inbox, so the
 * feature can be demonstrated without an internet connection.
 */
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');   // e.g. yourname@gmail.com
define('SMTP_PASS', '');   // the 16-character Google App Password

/* ---- 5. Uploads ------------------------------------------------------ */
define('MAX_UPLOAD_BYTES', 2 * 1024 * 1024);                    // 2MB
define('ALLOWED_IMAGE_EXT', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
