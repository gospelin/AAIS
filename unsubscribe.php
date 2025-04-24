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

try {
    global $pdo;

    if (!isset($_GET['email']) || empty($_GET['email'])) {
        $_SESSION['newsletter_flash_message'] = ['type' => 'error', 'message' => 'Invalid email address.'];
        header('Location: /index.php?url=home');
        exit;
    }

    $email = filter_var(urldecode($_GET['email']), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['newsletter_flash_message'] = ['type' => 'error', 'message' => 'Invalid email address.'];
        header('Location: /index.php?url=home');
        exit;
    }

    // Check if subscriber exists
    $stmt = $pdo->prepare('SELECT id FROM newsletter_subscribers WHERE email = :email AND is_verified = TRUE');
    $stmt->execute(['email' => $email]);
    $subscriber = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$subscriber) {
        $_SESSION['newsletter_flash_message'] = ['type' => 'error', 'message' => 'No active subscription found for this email.'];
        header('Location: /index.php?url=home');
        exit;
    }

    // Mark as unsubscribed
    $stmt = $pdo->prepare('UPDATE newsletter_subscribers SET unsubscribed_at = :unsubscribed_at WHERE id = :id');
    $stmt->execute([
        'unsubscribed_at' => (new DateTime())->format('Y-m-d H:i:s'),
        'id' => $subscriber['id'],
    ]);

    // Send unsubscription confirmation email
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
        $mail->Subject = 'Unsubscription Confirmation from Aunty Anne\'s International School';
        $mail->Body = '
            <p>Dear Subscriber,</p>
            <p>You have successfully unsubscribed from the Aunty Anne\'s International School newsletter. You will no longer receive our updates and news.</p>
            <p>If you change your mind, you can resubscribe at any time by visiting our website at <a href="https://auntyannesschools.com.ng">auntyannesschools.com.ng</a> and signing up for the newsletter again.</p>
            <p>If you have any questions or need assistance, please contact us at <a href="mailto:info@auntyannesschools.com.ng">info@auntyannesschools.com.ng</a> or call +234-806-967-8968.</p>
            <p>Thank you for your past interest in our community!</p>
            <p>Best regards,<br>Aunty Anne\'s International School</p>
        ';
        $mail->send();
    } catch (Exception $e) {
        error_log("[" . date('Y-m-d H:i:s') . "] Unsubscription confirmation email failed for $email: {$e->getMessage()}\n", 3, __DIR__ . '/error_log');
    }

    $_SESSION['newsletter_flash_message'] = ['type' => 'success', 'message' => 'You have successfully unsubscribed from our newsletter. A confirmation email has been sent to your inbox.'];
    header('Location: /index.php?url=home');
    exit;
} catch (Exception $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] Unsubscribe Error: {$e->getMessage()}\n", 3, __DIR__ . '/error_log');
    $_SESSION['newsletter_flash_message'] = ['type' => 'error', 'message' => 'An error occurred while unsubscribing. Please try again later.'];
    header('Location: /index.php?url=home');
    exit;
}
?>