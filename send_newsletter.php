<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Newsletter content (customize as needed)
$subject = "Aunty Anne's International School Newsletter - April 2025";
$htmlContent = '
    <h2>April 2025 Newsletter</h2>
    <p>Dear Subscriber,</p>
    <p>Welcome to the latest updates from Aunty Anne\'s International School!</p>
    <p>Here’s what’s happening this month:</p>
    <ul>
        <li>Upcoming Event: Annual Science Fair on May 10th</li>
        <li>New Program: After-School Coding Club</li>
        <li>Reminder: Parent-Teacher Conference on April 25th</li>
    </ul>
    <p>Stay tuned for more exciting news!</p>
    <p>Best regards,<br>Aunty Anne\'s International School</p>
    <p><small><a href="https://auntyannesschools.com.ng/unsubscribe.php?email={EMAIL}">Unsubscribe</a></small></p>
';

// Fetch verified subscribers
try {
    global $pdo;
    $stmt = $pdo->prepare('SELECT email FROM newsletter_subscribers WHERE is_verified = TRUE AND unsubscribed_at IS NULL');
    $stmt->execute();
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($subscribers)) {
        echo "No verified subscribers found.\n";
        exit;
    }

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'mail.auntyannesschools.com.ng';
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['SMTP_USERNAME'];
    $mail->Password = $_ENV['SMTP_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom('newsletter@auntyannesschools.com.ng', 'Aunty Anne\'s International School');
    $mail->addReplyTo('info@auntyannesschools.com.ng', 'Aunty Anne\'s International School');
    $mail->isHTML(true);
    $mail->Subject = $subject;

    foreach ($subscribers as $subscriber) {
        try {
            $mail->clearAddresses();
            $mail->addAddress($subscriber['email']);

            // Replace {EMAIL} in unsubscribe link with encoded email
            $unsubscribeLink = str_replace('{EMAIL}', urlencode($subscriber['email']), $htmlContent);
            $mail->Body = $unsubscribeLink;

            $mail->send();
            echo "Newsletter sent to {$subscriber['email']}\n";
        } catch (Exception $e) {
            error_log("[" . date('Y-m-d H:i:s') . "] Newsletter failed for {$subscriber['email']}: {$e->getMessage()}\n", 3, __DIR__ . '/error_log');
            echo "Failed to send to {$subscriber['email']}: {$e->getMessage()}\n";
        }
    }
} catch (Exception $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] Newsletter Script Error: {$e->getMessage()}\n", 3, __DIR__ . '/error_log');
    echo "Error: {$e->getMessage()}\n";
}
?>