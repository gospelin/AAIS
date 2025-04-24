<?php
// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<section id="newsletter-hero" class="py-5 text-center text-white" style="background: linear-gradient(135deg, #28a745, #155724);">
    <div class="container">
        <h1 class="display-4" data-aos="fade-down">Join Our Newsletter</h1>
        <p class="lead" data-aos="fade-up">Stay updated with the latest news and events from Aunty Anne's International School.</p>
    </div>
</section>

<section id="newsletter-form" class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6" data-aos="fade-up">
                <h2 class="section-heading mb-4 text-center">Subscribe Now</h2>
                <?php if (isset($_SESSION['newsletter_flash_message'])): ?>
                    <div class="alert <?= $_SESSION['newsletter_flash_message']['type'] === 'success' ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['newsletter_flash_message']['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['newsletter_flash_message']); ?>
                <?php endif; ?>
                <form action="/submit_newsletter.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name (Optional)</label>
                        <input type="text" class="form-control" id="name" name="name" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required maxlength="255">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
</section>