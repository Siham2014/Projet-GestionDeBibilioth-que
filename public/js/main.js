// filepath: /Projet-Cherradi/Projet-Cherradi/public/js/main.js
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling to services section
    const servicesButton = document.getElementById('servicesButton');
    if (servicesButton) {
        servicesButton.addEventListener('click', function(event) {
            event.preventDefault();
            const servicesSection = document.getElementById('servicesSection');
            if (servicesSection) {
                servicesSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
});