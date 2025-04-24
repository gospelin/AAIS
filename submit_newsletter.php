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

// Rate limiting: Allow only 5 submissions per hour per IP
$ip = $_SERVER['REMOTE_ADDR'];
$rateLimitKey = 'rate_limit_newsletter_' . md5($ip);
$rateLimitCount = $_SESSION[$rateLimitKey] ?? 0;
$rateLimitTime = $_SESSION[$rateLimitKey . '_time'] ?? 0;

if (time() - $rateLimitTime > 3600) {
    $rateLimitCount = 0;
    $rateLimitTime = time();
}

if ($rateLimitCount >= 5) {
    $_SESSION['newsletter_flash_message'] = ['type' => 'error', 'message' => 'Too many submissions. Please try again later.'];
    header('Location: /index.php?url=home');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("[" . date('Y-m-d H:i:s') . "] CSRF validation failed: POST[csrf_token]=" . ($_POST['csrf_token'] ?? 'not set') . ", SESSION[csrf_token]=" . ($_SESSION['csrf_token'] ?? 'not set') . "\n", 3, __DIR__ . '/error_log');
        $_SESSION['newsletter_flash_message'] = ['type' => 'error', 'message' => 'Invalid CSRF token.'];
        header('Location: /index.php?url=home');
        exit;
    }

    // Sanitize and validate input
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['newsletter_flash_message'] = ['type' => 'error', 'message' => 'Please enter a valid email address.'];
        header('Location: /index.php?url=home');
        exit;
    }

    // Length validation
    if (strlen($email) > 255) {
        $_SESSION['newsletter_flash_message'] = ['type' => 'error', 'message' => 'Email address is too long.'];
        header('Location: /index.php?url=home');
        exit;
    }

    try {
        global $pdo;

        // Check if email already exists
        $stmt = $pdo->prepare('SELECT id, is_verified, unsubscribed_at FROM newsletter_subscribers WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $subscriber = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($subscriber && $subscriber['is_verified'] && $subscriber['unsubscribed_at'] === null) {
            // User is already verified and not unsubscribed
            $_SESSION['newsletter_flash_message'] = ['type' => 'info', 'message' => 'You are already subscribed to our newsletter.'];
            header('Location: /index.php?url=home');
            exit;
        }

        // Generate verification token
        $verificationToken = bin2hex(random_bytes(32));

        if ($subscriber) {
            // Update existing record (unsubscribed or unverified)
            $stmt = $pdo->prepare('
                UPDATE newsletter_subscribers
                SET verification_token = :token, is_verified = FALSE, unsubscribed_at = NULL, subscribed_at = :subscribed_at
                WHERE email = :email
            ');
            $stmt->execute([
                'token' => $verificationToken,
                'email' => $email,
                'subscribed_at' => (new DateTime())->format('Y-m-d H:i:s'),
            ]);
        } else {
            // Insert new record
            $stmt = $pdo->prepare('
                INSERT INTO newsletter_subscribers (email, verification_token, subscribed_at)
                VALUES (:email, :token, :subscribed_at)
            ');
            $stmt->execute([
                'email' => $email,
                'token' => $verificationToken,
                'subscribed_at' => (new DateTime())->format('Y-m-d H:i:s'),
            ]);
        }

        // Send verification email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'mail.auntyannesschools.com.ng';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('no-reply@auntyannesschools.com.ng', 'Aunty Anne\'s International School');
            $mail->addAddress($email);
            $mail->addReplyTo('info@auntyannesschools.com.ng', 'Aunty Anne\'s International School');
            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Newsletter Subscription';
            $verificationLink = "https://auntyannesschools.com.ng/verify_newsletter.php?token=$verificationToken";
            $mail->Body = '
                <p>Dear Subscriber,</p>
                <p>Thank you for subscribing to our newsletter! Please verify your email address by clicking the link below:</p>
                <p><a href="' . $verificationLink . '">Verify Your Subscription</a></p>
                <p>If you did not request this subscription, please ignore this email.</p>
                <p>Best regards,<br>Aunty Anne\'s International School</p>
            ';
            $mail->send();
        } catch (Exception $e) {
            error_log("[" . date('Y-m-d H:i:s') . "] Verification email failed for $email: {$e->getMessage()}\n", 3, __DIR__ . '/error_log');
            $_SESSION['newsletter_flash_message'] = ['type' => 'error', 'message' => 'An error occurred while sending the verification email. Please try again later.'];
            header('Location: /index.php?url=home');
            exit;
        }

        // Update rate limit
        $rateLimitCount++;
        $_SESSION[$rateLimitKey] = $rateLimitCount;
        $_SESSION[$rateLimitKey . '_time'] = $rateLimitTime;

        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);

        // Clear CSRF token and set flash message
        unset($_SESSION['csrf_token']);
        $_SESSION['newsletter_flash_message'] = ['type' => 'success', 'message' => 'A verification email has been sent to your inbox. Please check your email to confirm your subscription.'];
        header('Location: /index.php?url=home');
        exit;
    } catch (Exception $e) {
        error_log("[" . date('Y-m-d H:i:s') . "] Newsletter Subscription Error: {$e->getMessage()}\n", 3, __DIR__ . '/error_log');
        $_SESSION['newsletter_flash_message'] = ['type' => 'error', 'message' => 'An error occurred while processing your subscription. Please try again later.'];
        header('Location: /index.php?url=home');
        exit;
    }
} else {
    header('Location: /index.php?url=home');
    exit;
}
?>