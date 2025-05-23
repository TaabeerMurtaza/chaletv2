const tabLinks = document.querySelectorAll('.tab-link');
const tabContents = document.querySelectorAll('.tab-content');

tabLinks.forEach(tabLink => {
    tabLink.addEventListener('click', () => {
        const tabId = tabLink.dataset.tab;

        tabLinks.forEach(link => {
            link.classList.remove('active');
        });
        tabContents.forEach(content => {
            content.classList.remove('active');
        });

        tabLink.classList.add('active');
        document.getElementById(tabId).classList.add('active');

    });
});

// Optional: Set the first tab as active by default
if (tabLinks.length > 0) {
    tabLinks[0].click();
}

document.addEventListener('DOMContentLoaded', function () {
    const openBtns = document.querySelectorAll('.openPanel'); // multiple open buttons
    const sidepanel = document.querySelector('.sildepanel');   // fixed typo
    const overlay = document.querySelector('.tab-overlay');
    const tabLinks = document.querySelectorAll('.tab-link');  // plural

    // Show side panel on any open button click
    openBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            sidepanel.classList.add('active');
            overlay.classList.add('active');
            console.log("asdasdasd");

        });
    });

    // Hide side panel on overlay click
    overlay.addEventListener('click', function () {
        sidepanel.classList.remove('active');
        overlay.classList.remove('active');
    });

    // Hide panel on tab link click
    tabLinks.forEach(function (tab) {
        tab.addEventListener('click', function () {
            sidepanel.classList.remove('active');
            overlay.classList.remove('active');
        });
    });
});
function toggleMenu() {
    const menu = document.getElementById('main-nav-db');
    menu.classList.toggle('active');
}

const ratingContainers = document.querySelectorAll('.rating-value');

ratingContainers.forEach(container => {
    const rating = parseInt(container.getAttribute('rating-value'), 10);
    const stars = container.querySelectorAll('i[stars]');

    stars.forEach((star, index) => {
        const isFilled = index < rating;
        star.innerHTML = `
      <svg class="star ${isFilled ? 'filled' : ''}" viewBox="0 0 24 24">
        <path d="M12 .587l3.668 7.568 8.332 1.151-6.001 5.838 1.415 8.204L12 18.896l-7.414 4.452 1.415-8.204-6.001-5.838 8.332-1.151z"/>
      </svg>
    `;
    });
});