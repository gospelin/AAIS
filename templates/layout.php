<?php
$title = isset($title) ? $title : "Welcome to Aunty Anne's International School";
$school_name = "Aunty Anne's International School";
$current_url = $_SERVER['REQUEST_URI'];
$page_name = basename($current_url, ".php");

// Ensure $content is a valid file path within templates/
$content_file = isset($content) && file_exists($content) ? $content : "templates/home.php";

// Determine the active page based on the URL
$active_page = isset($_GET['url']) ? $_GET['url'] : 'home';
if ($current_url === '/' || $current_url === '/index.php') {
    $active_page = 'home';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title; ?></title>
  <meta name="description" content="Explore Aunty Anne's International School – dedicated to practical learning, knowledge, and confidence-building for young minds.">
  <meta name="keywords" content="Aunty Anne's International School, Aunty Anne's, Auntie Anne's, Aunty Annes, Aunty Anne's Int'l School, AAIS, Best Schools in Nigeria, Private Schools in Aba, Best Schools in Aba, Best Schools in Abia, Education in Aba, Knowledge and Confidence">
  <meta name="author" content="Aunty Anne's International School">
  <meta property="og:title" content="<?php echo $title; ?> | Aunty Anne's International School">
  <meta property="og:description" content="In Aunty Anne's International School, we provide education focused on practical learning and confidence-building.">
  <meta property="og:image" content="/static/images/favicons/favicon.ico">
  <meta property="og:url" content="<?php echo $current_url; ?>">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:image" content="/static/images/favicons/favicon-96x96.png">
  <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"></noscript>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lora:wght@400;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="/static/css/style.css">
  <!-- Conditionally Include about.css Only for About Us Page -->
  <?php if ($page_name == "about_us"): ?>
      <link rel="stylesheet" href="static/css/about_us.css">
  <?php endif; ?>
  
  <link rel="icon" type="image/png" href="/static/images/favicons/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="/static/images/favicons/favicon.svg" />
  <link rel="shortcut icon" href="/static/images/favicons/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="/static/images/favicons/apple-touch-icon.png" />
  <link rel="manifest" href="/static/images/favicons/site.webmanifest" />
  <link rel="canonical" href="https://auntyannesschools.com.ng">
  <!-- Reference to sitemap.xml for search engines -->
  <link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml">
  <!-- Note: robots.txt is located at https://auntyannesschools.com.ng/robots.txt -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" />
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js" defer></script>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-9CLQVHQ1D2"></script>
  <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-9CLQVHQ1D2');
  </script>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "School",
    "name": "Aunty Anne's International School",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "No 6 Oomnne Drive by winner's bus-stop opposite Ngwa high school",
      "addressLocality": "Abayi, Aba",
      "addressRegion": "Abia",
      "addressCountry": "NG"
    },
    "telephone": "+234-806-967-8968",
    "email": "auntyannesschools@gmail.com",
    "url": "https://auntyannesschools.com.ng",
    "logo": {
      "@type": "ImageObject",
      "url": "https://auntyannesschools.com.ng/static/images/school_logo.png",
      "description": "Aunty Anne's International School Logo"
    },
    "founder": {
      "@type": "Person",
      "name": "Mrs. Anne Isaac"
    },
    "sameAs": [
      "https://facebook.com/auntyannesschools",
      "https://twitter.com/auntyannesschools",
      "https://instagram.com/auntyannesschools"
    ]
  }
  </script>
  <!-- Keep Swiper.js if still used elsewhere (e.g., testimonials) -->
  <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />-->
  <!--<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>-->
</head>
<body>
  <div class="d-flex flex-column min-vh-100">
    <header class="sticky-top">
      <nav class="navbar navbar-expand-xl navbar-light bg-white shadow-sm">
        <div class="container">
          <a class="school-name navbar-brand d-flex align-items-center" href="/">
            <img src="/static/images/school_logo.png" alt="School Logo" loading="lazy" class="d-inline-block align-top" style="height: 50px; margin-right: 10px;">
            <?php echo $school_name; ?>
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="toggler-icon"></span>
            <span class="toggler-icon"></span>
            <span class="toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
              <li class="nav-item">
                <a class="nav-link <?php echo $active_page === 'home' ? 'active' : ''; ?>" href="/">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $active_page === 'about_us' ? 'active' : ''; ?>" href="/about_us">About Us</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $active_page === 'admissions' ? 'active' : ''; ?>" href="/admissions">Admissions</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $active_page === 'programs' ? 'active' : ''; ?>" href="/programs">Programs</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $active_page === 'news_events' ? 'active' : ''; ?>" href="/news_events">News</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $active_page === 'gallery' ? 'active' : ''; ?>" href="/gallery">Gallery</a>
              </li>
              <!--<li class="nav-item">-->
              <!--  <a class="nav-link <?php echo $active_page === 'faqs' ? 'active' : ''; ?>" href="/faqs">FAQs</a>-->
              <!--</li>-->
              <li class="nav-item">
                <a class="nav-link <?php echo $active_page === 'contact' ? 'active' : ''; ?>" href="/contact">Contact Us</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="https://gigo.pythonanywhere.com/portal/login">Portal</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
    <main class="flex-fill">
      <?php include($content_file); ?>
    </main>
    <footer class="footer bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4 text-center text-md-start">
                <h5>Contact Us</h5>
                <p>No 6 Oomnne Drive, Abayi, Aba</p>
                <p><i class="fas fa-phone-alt"></i> +234-806-967-8968</p>
                <p><i class="fas fa-envelope"></i> <a href="mailto:auntyannesschools@gmail.com" class="text-white">auntyannesschools@gmail.com</a></p>
            </div>
            <div class="col-md-4 text-center text-md-start">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="/" class="text-white">Home</a></li>
                    <li><a href="/about_us" class="text-white">About Us</a></li>
                    <li><a href="/admissions" class="text-white">Admissions</a></li>
                    <li><a href="/programs" class="text-white">Programs</a></li>
                    <li><a href="/news_events" class="text-white">News & Events</a></li>
                    <li><a href="/gallery" class="text-white">Gallery</a></li>
                    <li><a href="/faqs" class="text-white">FAQs</a></li>
                    <li><a href="/contact" class="text-white">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-md-4 text-center text-md-start">
                <h5>Follow Us</h5>
                <div class="social-icons mb-3">
                    <a href="https://facebook.com/auntyannesschools" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://twitter.com/auntyannesschools" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="https://instagram.com/auntyannesschools" target="_blank"><i class="fab fa-instagram"></i></a>
                </div>
                <h5>Newsletter</h5>
                <?php if (isset($_SESSION['newsletter_flash_message'])): ?>
                    <div class="alert <?= $_SESSION['newsletter_flash_message']['type'] === 'success' ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['newsletter_flash_message']['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['newsletter_flash_message']); ?>
                <?php endif; ?>
                <form action="/submit_newsletter.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))); ?>">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Your Email" required maxlength="255">
                        <button class="btn btn-primary" type="submit">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center mt-3">
            <p>© <?php echo date('Y'); ?> <?php echo $school_name; ?>. All Rights Reserved.</p>
        </div>
    </div>
</footer>
  </div>
  <!-- Back to Top Button (retained) -->
  <button id="back-to-top" class="btn btn-primary">↑</button>
  <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    window.addEventListener('scroll', () => {
      const navbar = document.querySelector('.navbar');
      navbar.classList.toggle('scrolled', window.scrollY > 50);
    });
    // Back to Top Script (retained)
    document.addEventListener('DOMContentLoaded', () => {
      const backToTop = document.getElementById('back-to-top');
      window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
          backToTop.style.opacity = '1';
          backToTop.style.visibility = 'visible';
        } else {
          backToTop.style.opacity = '0';
          backToTop.style.visibility = 'hidden';
        }
      });
      backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    });
    // Initialize AOS after DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
      if (typeof AOS !== 'undefined') {
        AOS.init({
          duration: 1200,
          once: true,
          offset: 50,
          // disable: window.innerWidth < 768
        });
      } else {
        console.error('AOS library not loaded');
      }
    });
  </script>
</body>
</html>