// Ensure GSAP and ScrollTrigger are loaded before running animations
if (typeof gsap !== "undefined" && typeof ScrollTrigger !== "undefined") {
    gsap.registerPlugin(ScrollTrigger);
}

// Hero section animations
const initHeroAnimations = () => {
    const tl = gsap.timeline({ defaults: { ease: "power3.out" } });
    const fadeDown = document.querySelector(".gsap-fade-down");
    const fadeUps = document.querySelectorAll(".gsap-fade-up");
    const zoomIns = document.querySelectorAll(".gsap-zoom-in");

    if (fadeDown) {
        tl.from(fadeDown, {
            y: -50,
            opacity: 0,
            duration: 0.5,
            delay: 0.3,
        });
    }

    if (fadeUps.length) {
        tl.from(
            fadeUps,
            {
                y: 50,
                opacity: 0,
                duration: 0.5,
                stagger: 0.3,
            },
            "-=0.7"
        );
    }

    if (zoomIns.length) {
        tl.from(
            zoomIns,
            {
                scale: 0.9,
                opacity: 0,
                duration: 0.5,
            },
            "-=0.5"
        );
    }
};

// Interactive hero elements
const initHeroInteraction = () => {
    const hero = document.querySelector(".hero");
    const elements = document.querySelectorAll(".floating-element");
    if (hero && elements.length) {
        hero.addEventListener("mousemove", (e) => {
            elements.forEach((el) => {
                const speed = el.dataset.parallaxSpeed || 0.2;
                gsap.to(el, {
                    x: (e.clientX - window.innerWidth / 2) * speed,
                    y: (e.clientY - window.innerHeight / 2) * speed,
                    duration: 0.5,
                    ease: "power2.out",
                });
            });
        });
    }
};

// Scroll animations with batching
const initScrollAnimations = () => {
    ScrollTrigger.batch(".gsap-fade-up", {
        onEnter: (elements) =>
            gsap.from(elements, {
                y: 60,
                opacity: 0,
                duration: 0.5,
                stagger: 0.15,
            }),
        start: "top 85%",
        toggleActions: "play none none reverse",
    });

    ScrollTrigger.batch(".gsap-zoom-in", {
        onEnter: (elements) =>
            gsap.from(elements, {
                scale: 0.9,
                opacity: 0,
                duration: 0.9,
                stagger: 0.15,
            }),
        start: "top 85%",
        toggleActions: "play none none reverse",
    });
};

// Card hover animations with event delegation
const initCardHover = () => {
    const cardContainer = document.querySelector(".container");
    if (cardContainer) {
        cardContainer.addEventListener(
            "mouseenter",
            (e) => {
                if (e.target.classList.contains("card")) {
                    gsap.to(e.target, {
                        y: -10,
                        boxShadow: "0 15px 30px rgba(0, 0, 0, 0.2)",
                        duration: 0.3,
                        ease: "power2.out",
                    });
                }
            },
            true
        );

        cardContainer.addEventListener(
            "mouseleave",
            (e) => {
                if (e.target.classList.contains("card")) {
                    gsap.to(e.target, {
                        y: 0,
                        boxShadow: "0 4px 15px rgba(0, 0, 0, 0.1)",
                        duration: 0.3,
                        ease: "power2.out",
                    });
                }
            },
            true
        );
    }
};

// Button click animations
const initButtonClicks = () => {
    document.querySelectorAll(".btn").forEach((btn) => {
        btn.addEventListener("click", () => {
            gsap.to(btn, {
                scale: 0.95,
                duration: 0.1,
                yoyo: true,
                repeat: 1,
                ease: "power2.out",
            });
        });
    });
};

// Carousel functionality
const initCarousel = () => {
    const carousel = document.querySelector("#testimonialCarousel");
    if (!carousel) return;

    const items = carousel.querySelectorAll(".carousel-item");
    const indicators = carousel.querySelectorAll(".carousel-indicators button");
    const prevBtn = carousel.querySelector(".carousel-control-prev");
    const nextBtn = document.querySelector(".carousel-control-next");
    let currentIndex = 0;
    let intervalId = null;
    let isPaused = false;

    const showSlide = (index) => {
        items.forEach((item, i) => {
            item.classList.toggle("active", i === index);
            indicators[i].classList.toggle("active", i === index);
        });
        currentIndex = index;
    };

    const startAutoSlide = () => {
        if (!intervalId) {
            intervalId = setInterval(() => {
                if (!isPaused) {
                    currentIndex =
                        currentIndex === items.length - 1
                            ? 0
                            : currentIndex + 1;
                    showSlide(currentIndex);
                }
            }, 5000);
        }
    };

    const pauseAutoSlide = () => {
        isPaused = true;
        clearInterval(intervalId);
        intervalId = null;
        setTimeout(startAutoSlide, 10000);
    };

    prevBtn.addEventListener("click", () => {
        pauseAutoSlide();
        currentIndex = currentIndex === 0 ? items.length - 1 : currentIndex - 1;
        showSlide(currentIndex);
    });

    nextBtn.addEventListener("click", () => {
        pauseAutoSlide();
        currentIndex = currentIndex === items.length - 1 ? 0 : currentIndex + 1;
        showSlide(currentIndex);
    });

    indicators.forEach((indicator, i) => {
        indicator.addEventListener("click", () => {
            pauseAutoSlide();
            showSlide(i);
        });
    });

    document.addEventListener("visibilitychange", () => {
        isPaused = document.visibilityState === "hidden";
    });

    startAutoSlide();
};

// Sidebar navigation
const initSidebar = () => {
    const toggler = document.querySelector(".navbar-toggler");
    const sidebar = document.querySelector("#sidebarNav");
    const backdrop = document.querySelector("#sidebarBackdrop");
    const navLinks = document.querySelectorAll(".sidebar .nav-link");

    if (!toggler || !sidebar || !backdrop) return;

    const toggleSidebar = (show) => {
        gsap.to(sidebar, {
            x: show ? 0 : "-100%",
            duration: 0.4,
            ease: "power3.out",
            onStart: () => {
                sidebar.classList.toggle("show", show);
                backdrop.classList.toggle("show", show);
                sidebar.setAttribute("aria-hidden", !show);
                toggler.setAttribute("aria-expanded", show);
            },
        });
        gsap.to(backdrop, {
            opacity: show ? 1 : 0,
            visibility: show ? "visible" : "hidden",
            duration: 0.4,
            ease: "power3.out",
        });
        gsap.to(navLinks, {
            x: show ? 0 : -20,
            scale: show ? 1 : 0.95,
            opacity: show ? 1 : 0,
            duration: 0.3,
            stagger: 0.05,
            ease: "power2.out",
        });
    };

    toggler.addEventListener("click", () => {
        const isOpen = sidebar.classList.contains("show");
        toggleSidebar(!isOpen);
        if (!isOpen) {
            trapFocus(sidebar);
            navLinks[0].focus();
        }
    });

    backdrop.addEventListener("click", () => {
        toggleSidebar(false);
    });

    navLinks.forEach((link) => {
        link.addEventListener("click", () => {
            toggleSidebar(false);
        });
    });

    const trapFocus = (element) => {
        const focusableElements = element.querySelectorAll(
            'a[href], button, input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        const firstFocusable = focusableElements[0];
        const lastFocusable = focusableElements[focusableElements.length - 1];

        element.addEventListener("keydown", (e) => {
            if (e.key === "Tab") {
                if (e.shiftKey && document.activeElement === firstFocusable) {
                    e.preventDefault();
                    lastFocusable.focus();
                } else if (
                    !e.shiftKey &&
                    document.activeElement === lastFocusable
                ) {
                    e.preventDefault();
                    firstFocusable.focus();
                }
            }
        });
    };

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && sidebar.classList.contains("show")) {
            toggleSidebar(false);
            toggler.focus();
        }
    });
};

// Navbar scroll effect
const initNavbarScroll = () => {
    ScrollTrigger.create({
        trigger: document.body,
        start: "top top",
        end: "+=50",
        toggleClass: { targets: ".navbar", className: "scrolled" },
    });
};

// Back to top button
const initBackToTop = () => {
    const backToTop = document.getElementById("back-to-top");
    if (backToTop) {
        ScrollTrigger.create({
            trigger: document.body,
            start: "top+=300 top",
            onEnter: () => {
                gsap.to(backToTop, {
                    opacity: 1,
                    visibility: "visible",
                    duration: 0.3,
                    ease: "power2.out",
                });
            },
            onLeaveBack: () => {
                gsap.to(backToTop, {
                    opacity: 0,
                    visibility: "hidden",
                    duration: 0.3,
                    ease: "power2.out",
                });
            },
        });

        backToTop.addEventListener("click", (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: "smooth",
            });
            backToTop.blur();
        });

        backToTop.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: "smooth",
                });
            }
        });
    }
};

// Floating CTA
const initFloatingCTA = () => {
    const cta = document.querySelector(".floating-cta");
    if (cta) {
        ScrollTrigger.create({
            trigger: document.body,
            start: "top+=300 top",
            onEnter: () => {
                gsap.to(cta, {
                    opacity: 1,
                    visibility: "visible",
                    duration: 0.3,
                    ease: "power2.out",
                });
            },
            onLeaveBack: () => {
                gsap.to(cta, {
                    opacity: 0,
                    visibility: "hidden",
                    duration: 0.3,
                    ease: "power2.out",
                });
            },
        });
    }
};

// Initialize animations
document.addEventListener("DOMContentLoaded", () => {
    if (typeof gsap !== "undefined" && typeof ScrollTrigger !== "undefined") {
        initHeroAnimations();
        initHeroInteraction();
        initScrollAnimations();
        initCardHover();
        initNavbarScroll();
        initBackToTop();
        initFloatingCTA();
        initSidebar();
        initButtonClicks();
    }
    initCarousel();
});

// Cleanup on page unload
window.addEventListener("beforeunload", () => {
    ScrollTrigger.getAll().forEach((trigger) => trigger.kill());
});

