import './bootstrap';
import Alpine from "alpinejs";
import { gsap } from "gsap";
import { ScrollTrigger, TextPlugin } from "gsap/all";

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger, TextPlugin);

// Expose libraries globally


window.gsap = gsap;

window.ScrollTrigger = ScrollTrigger;
window.TextPlugin = TextPlugin;

window.Alpine = Alpine;

Alpine.start();

// Import animations
import './animations';
