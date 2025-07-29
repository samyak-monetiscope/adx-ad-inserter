// admin-scripts.js
// Tab‐switching logic for Monetiscope Ad Inserter settings

document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.nav-tab');
    const tabContents = document.querySelectorAll('.adx-tab');

    function hideAllTabs() {
        tabContents.forEach(function(content) {
            content.style.display = 'none';
        });
        tabs.forEach(function(tab) {
            tab.classList.remove('nav-tab-active');
        });
    }

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = tab.getAttribute('data-target');
            hideAllTabs();

            // Activate clicked tab
            tab.classList.add('nav-tab-active');

            // Show corresponding content
            const contentDiv = document.getElementById(targetId);
            if (contentDiv) {
                contentDiv.style.display = 'block';
            }
        });
    });

    // Initialize: show first tab (Popup)
    hideAllTabs();
    document.querySelector('.nav-tab[data-target="tab-display-slot"]').classList.add('nav-tab-active');
    document.getElementById('tab-display-slot').style.display = 'block';
});
