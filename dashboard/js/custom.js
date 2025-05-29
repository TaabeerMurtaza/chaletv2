// const tabLinks = document.querySelectorAll('.tab-link');

function showContent(event, button) {
    event.preventDefault();

    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(el => {
        el.style.display = 'none';
    });

    // Remove 'active' from all tab links
    const allTabLinks = document.querySelectorAll('.tab-link-list');
    allTabLinks.forEach(link => link.classList.remove('active'));

    // Get target content and tab link
    const targetId = button.getAttribute('data-id');
    const targetElement = document.getElementById(targetId);
    const targetTab = document.getElementById(targetId + '-link');

    // Show the selected tab content
    if (targetElement) {
        targetElement.style.display = 'block';
    }

    // Add 'active' to all tabs before and including the clicked one
    let activate = true;
    allTabLinks.forEach(link => {
        if (activate) link.classList.add('active');
        if (link === targetTab) activate = false;
    });
}

document.querySelectorAll('.img-detail').forEach(card => {
    card.addEventListener('click', () => {
        card.classList.toggle('selected');
    });
});

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