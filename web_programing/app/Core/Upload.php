<?php

namespace App\Core;

/**
 * Checks and saves an uploaded image (the screenshot on a question).
 * The same rules are used by the create form and the edit form.
 */
class Upload
{
    public static function image(?array $file, ?string &$error): ?string
    {
        $error = null;

        // The field is optional: nothing selected is not an error.
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'The image could not be uploaded (PHP error code ' . $file['error'] . ').';
            return null;
        }

        // 1. Whitelist the file extension.
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_IMAGE_EXT, true)) {
            $error = 'Only JPG, JPEG, PNG, GIF or WEBP images are allowed.';
            return null;
        }

        // 2. Enforce the size limit (same 2 MB limit as the lab exercises).
        if ($file['size'] > MAX_UPLOAD_BYTES) {
            $error = 'The image is larger than the 2 MB limit.';
            return null;
        }

        // 3. getimagesize() fails for files that only pretend to be images.
        if (@getimagesize($file['tmp_name']) === false) {
            $error = 'The file does not look like a real image.';
            return null;
        }

        // 4. Rename the file so a name like "virus.php.jpg" can never clash
        //    or overwrite anything: it becomes e.g. img_66a1b2c3d4e5f.jpg
        $newName = uniqid('img_') . '.' . $ext;

        $dir = ROOT_PATH . '/public/uploads/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $dir . $newName)) {
            $error = 'The image could not be saved on the server.';
            return null;
        }

        return $newName;
    }

    // Delete a previously stored upload; missing files are ignored.
    public static function remove(?string $fileName): void
    {
        if ($fileName && is_file(ROOT_PATH . '/public/uploads/' . $fileName)) {
            unlink(ROOT_PATH . '/public/uploads/' . $fileName);
        }
    }
}