<?php
require_once dirname(__DIR__, 3) . '/wp-load.php';

if (!is_user_logged_in()) {
    status_header(403);
    exit('Login required');
}

if (empty($_GET['file'])) {
    status_header(400);
    exit('No file specified');
}

$file = wp_unslash($_GET['file']);
$file = ltrim($file, "/\\");
$file = str_replace('\\', '/', $file);

$base_dir = realpath(ABSPATH . '../e-learning-material');

if (!$base_dir || !is_dir($base_dir)) {
    status_header(500);
    exit('Base directory not found');
}

$video_path = realpath($base_dir . DIRECTORY_SEPARATOR . $file);
$base_prefix = rtrim($base_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

if (
    !$video_path ||
    !is_file($video_path) ||
    strncmp($video_path, $base_prefix, strlen($base_prefix)) !== 0
) {
    status_header(403);
    exit('Invalid file path');
}

$allowed_ext = ['mp4'];
$ext = strtolower(pathinfo($video_path, PATHINFO_EXTENSION));

if (!in_array($ext, $allowed_ext, true)) {
    status_header(403);
    exit('File type not allowed');
}

if (!is_readable($video_path)) {
    status_header(403);
    exit('File not readable');
}

$filesize = filesize($video_path);

if ($filesize === false || $filesize < 1) {
    status_header(404);
    exit('File not found');
}

$filename = basename($video_path);
$mime = 'video/mp4';

while (ob_get_level() > 0) {
    ob_end_clean();
}

@ini_set('zlib.output_compression', 'Off');
@set_time_limit(0);

$start = 0;
$end = $filesize - 1;
$status_code = 200;

if (isset($_SERVER['HTTP_RANGE']) && preg_match('/bytes=(\d*)-(\d*)/i', $_SERVER['HTTP_RANGE'], $matches)) {
    $range_start = $matches[1];
    $range_end = $matches[2];

    if ($range_start === '' && $range_end === '') {
        header('Content-Range: bytes */' . $filesize, true, 416);
        exit;
    }

    if ($range_start === '') {
        $suffix_length = (int) $range_end;

        if ($suffix_length <= 0) {
            header('Content-Range: bytes */' . $filesize, true, 416);
            exit;
        }

        $start = max(0, $filesize - $suffix_length);
        $end = $filesize - 1;
    } else {
        $start = (int) $range_start;
        $end = ($range_end === '') ? ($filesize - 1) : (int) $range_end;
    }

    if ($start < 0 || $start > $end || $start >= $filesize) {
        header('Content-Range: bytes */' . $filesize, true, 416);
        exit;
    }

    if ($end >= $filesize) {
        $end = $filesize - 1;
    }

    $status_code = 206;
}

$length = ($end - $start) + 1;

header('Content-Type: ' . $mime, true, $status_code);
header('Content-Disposition: inline; filename="' . addslashes($filename) . '"');
header('Accept-Ranges: bytes');
header('Content-Length: ' . $length);
header('Cache-Control: private, max-age=0, must-revalidate');
header('X-Content-Type-Options: nosniff');

if ($status_code === 206) {
    header("Content-Range: bytes $start-$end/$filesize");
}

$handle = fopen($video_path, 'rb');

if (!$handle) {
    status_header(500);
    exit('Could not open file');
}

if (fseek($handle, $start) !== 0) {
    fclose($handle);
    status_header(500);
    exit('Could not seek file');
}

$chunk_size = 1024 * 1024;
$bytes_left = $length;

while (!feof($handle) && $bytes_left > 0) {
    $read_length = min($chunk_size, $bytes_left);
    $buffer = fread($handle, $read_length);

    if ($buffer === false || $buffer === '') {
        break;
    }

    echo $buffer;
    flush();

    $bytes_left -= strlen($buffer);

    if (connection_status() !== CONNECTION_NORMAL) {
        break;
    }
}

fclose($handle);
exit;