<?php
session_start();
require_once 'config.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Custom validation function for verification token
function validate_verification_token($token) {
    // Token should be a 64-character hexadecimal string
    if (preg_match('/^[0-9a-f]{64}$/i', $token)) {
        return $token;
    }
    return false;
}

try {
    global $pdo;

    if (!isset($_GET['token']) || empty($_GET['token'])) {
        $_SESSION['newsletter_flash_message'] = ['type' => 'error', 'message' => 'Invalid verification token.'];
        header('Location: /index.php?url=home');
        exit;
    }

    $token = validate_verification_token($_GET['token']);

    if (!$token) {
        $_SESSION['newsletter_flash_message'] = ['type' => 'error', 'message' => 'Invalid verification token format.'];
        header('Location: /index.php?url=home');
        exit;
    }

    // Find subscriber by token
    $stmt = $pdo->prepare('SELECT id, email, is_verified FROM newsletter_subscribers WHERE verification_token = :token');
    $stmt->execute(['token' => $token]);
    $subscriber = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$subscriber) {
        $_SESSION['newsletter_flash_message'] = ['type' => 'error', 'message' => 'Invalid or expired verification token.'];
        header('Location: /index.php?url=home');
        exit;
    }

    if ($subscriber['is_verified']) {
        $_SESSION['newsletter_flash_message'] = ['type' => 'info', 'message' => 'Your subscription is already verified.'];
        header('Location: /index.php?url=home');
        exit;
    }

    // Verify the subscriber
    $stmt = $pdo->prepare('UPDATE newsletter_subscribers SET is_verified = TRUE, verification_token = NULL WHERE id = :id');
    $stmt->execute(['id' => $subscriber['id']]);

    // Send welcome email
    $welcomeMail = new PHPMailer(true);
    try {
        $welcomeMail->isSMTP();
        $welcomeMail->Host = 'mail.auntyannesschools.com.ng';
        $welcomeMail->SMTPAuth = true;
        $welcomeMail->Username = $_ENV['SMTP_USERNAME'];
        $welcomeMail->Password = $_ENV['SMTP_PASSWORD'];
        $welcomeMail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $welcomeMail->Port = 465;
        $welcomeMail->CharSet = 'UTF-8';

        $welcomeMail->setFrom('no-reply@auntyannesschools.com.ng', 'Aunty Anne\'s International School');
        $welcomeMail->addAddress($subscriber['email']);
        $welcomeMail->addReplyTo('info@auntyannesschools.com.ng', 'Aunty Anne\'s International School');
        $welcomeMail->isHTML(true);
        $welcomeMail->Subject = 'Welcome to Aunty Anne\'s International School Newsletter!';
        $welcomeMail->Body = '
            <p>Dear Subscriber,</p>
            <p>Welcome to the Aunty Anne\'s International School newsletter! Thank you for joining our community. You’re now subscribed to receive our latest updates, news, and events.</p>
            <p>Stay tuned for our next newsletter, where we’ll share exciting developments and opportunities at our school!</p>
            <p>If you have any questions, feel free to contact us at <a href="mailto:info@auntyannesschools.com.ng">info@auntyannesschools.com.ng</a> or call +234-806-967-8968.</p>
            <p>Best regards,<br>Aunty Anne\'s International School</p>
            <p><small><a href="https://auntyannesschools.com.ng/unsubscribe.php?email=' . urlencode($subscriber['email']) . '">Unsubscribe</a></small></p>
        ';
        $welcomeMail->send();
    } catch (Exception $e) {
        error_log("[" . date('Y-m-d H:i:s') . "] Welcome email failed for {$subscriber['email']}: {$e->getMessage()}\n", 3, __DIR__ . '/error_log');
    }

    $_SESSION['newsletter_flash_message'] = ['type' => 'success', 'message' => 'Your newsletter subscription has been verified! A welcome email has been sent to your inbox.'];
    header('Location: /index.php?url=home');
    exit;
} catch (Exception $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] Verification Error: {$e->getMessage()}\n", 3, __DIR__ . '/error_log');
    $_SESSION['newsletter_flash_message'] = ['type' => 'error', 'message' => 'An error occurred during verification. Please try again later.'];
    header('Location: /index.php?url=home');
    exit;
}
?>