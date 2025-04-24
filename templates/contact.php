<?php
// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<section id="contact-hero" class="py-5 text-center text-white" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
  <div class="container">
    <h1 class="display-4" data-aos="fade-down">Get in Touch</h1>
    <p class="lead" data-aos="fade-up">We’re here to answer your questions and assist you with any inquiries.</p>
  </div>
</section>

<section id="contact-details" class="py-5 bg-light">
  <div class="container">
    <div class="row">
      <div class="col-md-6" data-aos="fade-right">
        <h2 class="section-heading mb-4">Contact Information</h2>
        <p><i class="fas fa-map-marker-alt"></i> No 6 Oomnne Drive, Abayi, Aba, Abia, Nigeria</p>
        <p><i class="fas fa-phone-alt"></i> +234-806-967-8968</p>
        <p><i class="fas fa-envelope"></i> <a href="mailto:info@auntyannesschools.com.ng">info@auntyannesschools.com.ng</a></p>
        <div class="social-icons mt-4">
          <a href="https://facebook.com/auntyannesschools" target="_blank"><i class="fab fa-facebook-f"></i></a>
          <a href="https://twitter.com/auntyannesschools" target="_blank"><i class="fab fa-twitter"></i></a>
          <a href="https://instagram.com/auntyannesschools" target="_blank"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      <div class="col-md-6" data-aos="fade-left">
        <h2 class="section-heading mb-4">Send Us a Message</h2>
        
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert <?= $_SESSION['flash_message']['type'] === 'success' ? 'alert-success' : 'alert-danger' ?>" role="alert">
                <?= htmlspecialchars($_SESSION['flash_message']['message']) ?>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>
        
        <form action="/submit_contact.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" required maxlength="100">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required maxlength="255">
    </div>
    <div class="mb-3">
        <label for="message" class="form-label">Message</label>
        <textarea class="form-control" id="message" name="message" rows="5" required maxlength="1000"></textarea>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="newsletter" name="newsletter">
        <label class="form-check-label" for="newsletter">Subscribe to our newsletter</label>
    </div>
    <button type="submit" class="btn btn-primary">Send Message</button>
</form>
      </div>
    </div>
  </div>
</section>

<section id="contact-map" class="py-5">
  <div class="container">
    <h2 class="section-heading mb-4 text-center" data-aos="fade-up">Find Us</h2>
    <div class="map-container" data-aos="fade-up">
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d993.4329494977746!2d7.3309549798887925!3d5.146810527312397!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x10429b8a252a1025%3A0x9ac7285163476022!2sAunty%20Anne's%20International%20School%20Aba!5e0!3m2!1sen!2sus!4v1714916226431!5m2!1sen!2sus" width="100%" height="450" style="border: 0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
  </div>
</section>