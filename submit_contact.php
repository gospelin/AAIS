
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

// Custom sanitization function for text input (replaces FILTER_SANITIZE_STRING)
function sanitize_text_input($input) {
    // Remove leading/trailing whitespace, HTML tags, and control characters
    $input = trim(strip_tags($input));
    // Remove non-printable characters (except newline and tab)
    $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $input);
    return $input;
}

// Rate limiting: Allow only 5 submissions per hour per IP
$ip = $_SERVER['REMOTE_ADDR'];
$rateLimitKey = 'rate_limit_' . md5($ip);
$rateLimitCount = $_SESSION[$rateLimitKey] ?? 0;
$rateLimitTime = $_SESSION[$rateLimitKey . '_time'] ?? 0;

if (time() - $rateLimitTime > 3600) {
    $rateLimitCount = 0;
    $rateLimitTime = time();
}

if ($rateLimitCount >= 5) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Too many submissions. Please try again later.'];
    header('Location: /index.php?url=contact');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("[" . date('Y-m-d H:i:s') . "] CSRF validation failed: POST[csrf_token]=" . ($_POST['csrf_token'] ?? 'not set') . ", SESSION[csrf_token]=" . ($_SESSION['csrf_token'] ?? 'not set') . "\n", 3, __DIR__ . '/error_log');
        $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Invalid CSRF token.'];
        header('Location: /index.php?url=contact');
        exit;
    }

    // Sanitize and validate input
    $name = sanitize_text_input($_POST['name'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = trim(strip_tags($_POST['message'] ?? '')); // Preserve apostrophes, remove HTML tags
    $newsletter = isset($_POST['newsletter']) && $_POST['newsletter'] === 'on';

    // Additional validation
    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Invalid input. All fields are required, and email must be valid.'];
        header('Location: /index.php?url=contact');
        exit;
    }

    // Length validation
    if (strlen($name) > 100 || strlen($email) > 255 || strlen($message) > 1000) {
        $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Input exceeds maximum length.'];
        header('Location: /index.php?url=contact');
        exit;
    }

    try {
        global $pdo;

        // Save contact message to database
        $stmt = $pdo->prepare('
            INSERT INTO contact_messages (name, email, message, submitted_at)
            VALUES (:name, :email, :message, :submitted_at)
        ');
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'submitted_at' => (new DateTime())->format('Y-m-d H:i:s'),
        ]);

        // Handle newsletter subscription
        if ($newsletter) {
            // Check if email already exists
            $stmt = $pdo->prepare('SELECT id, is_verified, unsubscribed_at FROM newsletter_subscribers WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $subscriber = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($subscriber && $subscriber['is_verified'] && $subscriber['unsubscribed_at'] === null) {
                // User is already verified and not unsubscribed
                // No action needed, as they’re already subscribed
            } else {
                // Generate verification token
                $verificationToken = bin2hex(random_bytes(32));

                if ($subscriber) {
                    // Update existing record (unsubscribed or unverified)
                    $stmt = $pdo->prepare('
                        UPDATE newsletter_subscribers
                        SET name = :name, verification_token = :token, is_verified = FALSE, unsubscribed_at = NULL, subscribed_at = :subscribed_at
                        WHERE email = :email
                    ');
                    $stmt->execute([
                        'name' => $name,
                        'email' => $email,
                        'token' => $verificationToken,
                        'subscribed_at' => (new DateTime())->format('Y-m-d H:i:s'),
                    ]);
                } else {
                    // Insert new record
                    $stmt = $pdo->prepare('
                        INSERT INTO newsletter_subscribers (name, email, verification_token, subscribed_at)
                        VALUES (:name, :email, :verification_token, :subscribed_at)
                    ');
                    $stmt->execute([
                        'name' => $name,
                        'email' => $email,
                        'verification_token' => $verificationToken,
                        'subscribed_at' => (new DateTime())->format('Y-m-d H:i:s'),
                    ]);
                }

                // Send verification email
                $userMail = new PHPMailer(true);
                try {
                    $userMail->isSMTP();
                    $userMail->Host = 'mail.auntyannesschools.com.ng';
                    $userMail->SMTPAuth = true;
                    $userMail->Username = $_ENV['SMTP_USERNAME'];
                    $userMail->Password = $_ENV['SMTP_PASSWORD'];
                    $userMail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $userMail->Port = 465;
                    $userMail->CharSet = 'UTF-8';

                    $userMail->setFrom('no-reply@auntyannesschools.com.ng', 'Aunty Anne\'s International School');
                    $userMail->addAddress($email, $name);
                    $userMail->addReplyTo('info@auntyannesschools.com.ng', 'Aunty Anne\'s International School');
                    $userMail->isHTML(true);
                    $userMail->Subject = 'Verify Your Newsletter Subscription';
                    $verificationLink = "https://auntyannesschools.com.ng/verify_newsletter.php?token=$verificationToken";
                    $userMail->Body = '
                        <p>Dear ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ',</p>
                        <p>Thank you for subscribing to our newsletter! Please verify your email address by clicking the link below:</p>
                        <p><a href="' . $verificationLink . '">Verify Your Subscription</a></p>
                        <p>If you did not request this subscription, please ignore this email.</p>
                        <p>Best regards,<br>Aunty Anne\'s International School</p>
                    ';
                    $userMail->send();
                } catch (Exception $e) {
                    error_log("[" . date('Y-m-d H:i:s') . "] Newsletter verification email failed: {$e->getMessage()}\n", 3, __DIR__ . '/error_log');
                }
            }
        }

        // Send email to info@auntyannesschools.com.ng
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
            $mail->addAddress('info@auntyannesschools.com.ng');
            $mail->addReplyTo('info@auntyannesschools.com.ng', 'Aunty Anne\'s International School');
            $mail->isHTML(false);
            $mail->Subject = 'New Contact Form Submission';
            $mail->Body = "Name: $name\nEmail: $email\nNewsletter Opt-in: " . ($newsletter ? 'Yes' : 'No') . "\nMessage:\n$message";
            $mail->send();
        } catch (Exception $e) {
            throw new Exception("Email to info@ failed. Mailer Error: {$mail->ErrorInfo}");
        }

        // Send confirmation email to user
        $userMail = new PHPMailer(true);
        try {
            $userMail->isSMTP();
            $userMail->Host = 'mail.auntyannesschools.com.ng';
            $userMail->SMTPAuth = true;
            $userMail->Username = $_ENV['SMTP_USERNAME'];
            $userMail->Password = $_ENV['SMTP_PASSWORD'];
            $userMail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $userMail->Port = 465;
            $userMail->CharSet = 'UTF-8';

            $userMail->setFrom('no-reply@auntyannesschools.com.ng', 'Aunty Anne\'s International School');
            $userMail->addAddress($email, $name);
            $userMail->addReplyTo('info@auntyannesschools.com.ng', 'Aunty Anne\'s International School');
            $userMail->isHTML(true);
            $userMail->Subject = 'Thank You for Contacting Aunty Anne\'s International School';
            $userMail->Body = '
                <p>Dear ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ',</p>
                <p>Thank you for reaching out to Aunty Anne\'s International School. We have received your message and will respond shortly.</p>
                <p><strong>Your Message:</strong><br>' . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . '</p>
                ' . ($newsletter ? '<p>You have also opted to join our newsletter. Please check your inbox (and spam/junk folder) for a verification email to confirm your subscription.</p>' : '') . '
                <p>Please reply to this email or contact us at <a href="mailto:info@auntyannesschools.com.ng">info@auntyannesschools.com.ng</a> for further inquiries.</p>
                <p>Best regards,<br>Aunty Anne\'s International School<br>+234-806-967-8968<br><a href="https://auntyannesschools.com.ng">auntyannesschools.com.ng</a></p>
            ';
            $userMail->send();
        } catch (Exception $e) {
            error_log("[" . date('Y-m-d H:i:s') . "] User confirmation email failed: {$e->getMessage()}\n", 3, __DIR__ . '/error_log');
        }

        // Update rate limit
        $rateLimitCount++;
        $_SESSION[$rateLimitKey] = $rateLimitCount;
        $_SESSION[$rateLimitKey . '_time'] = $rateLimitTime;

        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);

        // Clear CSRF token and set flash message
        unset($_SESSION['csrf_token']);
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Message sent successfully! ' . ($newsletter ? 'Please check your email to verify your newsletter subscription.' : 'We will get back to you soon.')];
        header('Location: /index.php?url=contact');
        exit;
    } catch (Exception $e) {
        error_log("[" . date('Y-m-d H:i:s') . "] Contact Form Error: {$e->getMessage()}\n", 3, __DIR__ . '/error_log');
        $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'An error occurred while sending your message. Please try again later.'];
        header('Location: /index.php?url=contact');
        exit;
    }
} else {
    header('Location: /index.php?url=contact');
    exit;
}
?>