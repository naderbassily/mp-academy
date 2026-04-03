<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

// Get video file name
$file = $_GET['file'] ?? '';

// Basic security check
if (!$file) {
    status_header(403);
    exit('No file specified');
}

// ✅ Check if user is logged in
if (!is_user_logged_in()) {
    status_header(403);
    exit('Login required');
}

// ✅ OPTIONAL: LearnDash access check
// Replace 123 with your course ID
$course_id = 123;

if (!sfwd_lms_has_access($course_id)) {
    status_header(403);
    exit('No course access');
}

// 🔐 Path to your private folder
$video_path = '/home/youruser/e-learning-material/' . basename($file);

// Check file exists
if (!file_exists($video_path)) {
    status_header(404);
    exit('File not found');
}

// Serve video
header('Content-Type: video/mp4');
header('Content-Length: ' . filesize($video_path));
header('Accept-Ranges: bytes');

readfile($video_path);
exit;