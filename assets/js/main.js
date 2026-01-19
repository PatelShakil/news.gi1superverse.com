// Main JavaScript for News Site

// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', () => {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }
});

// Dark Mode Toggle
function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = html.classList.contains('dark');

    if (isDark) {
        html.classList.remove('dark');
        localStorage.setItem('darkMode', 'false');
    } else {
        html.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
    }
}

// Load dark mode preference on page load
document.addEventListener('DOMContentLoaded', () => {
    const darkMode = localStorage.getItem('darkMode');

    // Check for saved preference or default to light mode
    if (darkMode === 'true') {
        document.documentElement.classList.add('dark');
    } else if (darkMode === 'false') {
        document.documentElement.classList.remove('dark');
    } else {
        // Check system preference if no saved preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
        }
    }
});

// Smooth scroll to top
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Show scroll to top button
window.addEventListener('scroll', () => {
    const scrollBtn = document.getElementById('scroll-to-top');
    if (scrollBtn) {
        if (window.pageYOffset > 300) {
            scrollBtn.classList.remove('hidden');
        } else {
            scrollBtn.classList.add('hidden');
        }
    }
});

// Lazy load images
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    const images = document.querySelectorAll('img[data-src]');
    images.forEach(img => imageObserver.observe(img));
}

console.log('News Site Loaded Successfully');
