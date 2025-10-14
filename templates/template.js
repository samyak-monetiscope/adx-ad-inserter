// admin-scripts.js
// Tab‐switching logic for Monetiscope Ad Inserter settings
console.log("from template.js")
console.warn("from template.js")

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


//settings-template.php
document.addEventListener('DOMContentLoaded', function(){
  var toggle = document.getElementById('adx_enabled');
  var title  = document.querySelector('.toggle-title');
  toggle.addEventListener('change', function(){
    title.textContent = this.checked ? 'Plugin Active' : 'Plugin Inactive';
  });
});

// custom js
 document.querySelectorAll(".custom-tab-toggle").forEach(btn => {
    btn.addEventListener("click", function () {
        document.querySelectorAll(".custom-tab-toggle").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        const tab = btn.getAttribute("data-tab");
        document.getElementById("custom-code-header").style.display = tab === "header" ? "block" : "none";
        document.getElementById("custom-code-footer").style.display = tab === "footer" ? "block" : "none";
        document.getElementById("custom-ads-txt").style.display = tab === "ads-txt" ? "block" : "none";
    });
});


// display js
document.addEventListener('DOMContentLoaded', function () {
  const tabs = document.querySelectorAll('.display-tab');
  const contents = document.querySelectorAll('.display-content');
  tabs.forEach((tab, idx) => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      contents.forEach(c => c.classList.remove('active'));
      tab.classList.add('active');
      contents[idx].classList.add('active');
    });
  });
  if (tabs.length > 0) { tabs[0].click(); }
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[id^="display_slot_"][id$="_insertion"]').forEach(select => {
    select.addEventListener('change', e => {
        const idx = e.target.id.match(/\d+/)[0];
        const wrapper = document.getElementById(`display_slot_${idx}_offset`).closest('.offset-wrapper');
        if (['before_paragraph','after_paragraph','before_image','after_image'].includes(e.target.value)) {
        wrapper.style.display = '';
        } else {
        wrapper.style.display = 'none';
        }
    });
    });
});

//fling cartpet
document.addEventListener('DOMContentLoaded', function () {
    const insertion = document.getElementById('flying_insertion');
    const offsetWrap = document.getElementById('flying-offset-wrapper');

    function toggleOffset() {
    if (['before_paragraph','after_paragraph','before_image','after_image'].includes(insertion.value)) {
        offsetWrap.style.display = '';
    } else {
        offsetWrap.style.display = 'none';
    }
    }

    insertion.addEventListener('change', toggleOffset);
    toggleOffset(); // initial state
});

