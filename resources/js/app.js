import './bootstrap';
import Alpine from 'alpinejs';
import AOS from 'aos';
import 'aos/dist/aos.css';

// Initialize AOS (Animate On Scroll)
AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true,
    offset: 100,
});

// Make Alpine globally available
window.Alpine = Alpine;

// Start Alpine.js
Alpine.start();
