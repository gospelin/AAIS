<?php
session_start(); // Start session early
require_once 'config.php';


// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get the URL parameter and sanitize it
$url = isset($_GET['url']) ? rtrim(strtolower($_GET['url']), '/') : 'home';

// Define the content file path based on the URL
$content_file = "templates/{$url}.php";

// Check if the content file exists; if not, load a 404 page
if (!file_exists($content_file)) {
    $url = '404';
    $content_file = "templates/404.php";
}

// Set the page title dynamically based on the URL
$page_titles = [
    'home' => "Home | " . SCHOOL_NAME,
    'about_us' => "About Us | " . SCHOOL_NAME,
    'admissions' => "Admissions | " . SCHOOL_NAME,
    'programs' => "Programs | " . SCHOOL_NAME,
    'news_events' => "News & Events | " . SCHOOL_NAME,
    'gallery' => "Gallery | " . SCHOOL_NAME,
    'faqs' => "FAQs | " . SCHOOL_NAME,
    'contact' => "Contact Us | " . SCHOOL_NAME,
    'newsletter' => "Newsletter | " . SCHOOL_NAME,
    '404' => "Page Not Found | " . SCHOOL_NAME
];
$title = isset($page_titles[$url]) ? $page_titles[$url] : ucwords(str_replace('_', ' ', $url)) . " | " . SCHOOL_NAME;

// Pass the content file to the layout
$content = $content_file;
include('templates/layout.php');
?>