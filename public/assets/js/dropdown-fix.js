/**
 * AGGRESSIVE DROPDOWN POSITIONING FIX FOR MOBILE DEVICES
 * This script forces absolute positioning on all dropdowns for small screens
 */

(function() {
    'use strict';

    // Function to force dropdown positioning
    function forceDropdownPositioning() {
        // Only apply on small screens
        if (window.innerWidth <= 576) {
            // Get all dropdown menus
            const dropdowns = document.querySelectorAll(
                '.dropdown-menu, .dropdown-menu-scroll, .notification-container, .dropdown-content'
            );

            dropdowns.forEach(function(dropdown) {
                // Force absolute positioning
                dropdown.style.setProperty('position', 'absolute', 'important');
                dropdown.style.setProperty('top', '100%', 'important');
                dropdown.style.setProperty('left', '0', 'important');
                dropdown.style.setProperty('right', 'auto', 'important');
                dropdown.style.setProperty('bottom', 'auto', 'important');
                dropdown.style.setProperty('transform', 'none', 'important');
                dropdown.style.setProperty('z-index', '99999', 'important');
                dropdown.style.setProperty('float', 'none', 'important');
                dropdown.style.setProperty('width', 'auto', 'important');
                dropdown.style.setProperty('min-width', '160px', 'important');
                dropdown.style.setProperty('max-width', '300px', 'important');

                // Style the dropdown
                if (!dropdown.classList.contains('show')) {
                    dropdown.style.setProperty('display', 'none', 'important');
                } else {
                    dropdown.style.setProperty('display', 'block', 'important');
                    dropdown.style.setProperty('opacity', '1', 'important');
                    dropdown.style.setProperty('visibility', 'visible', 'important');
                }

                // Ensure parent containers allow overflow
                let parent = dropdown.parentElement;
                while (parent && parent !== document.body) {
                    if (parent.classList.contains('dropdown') ||
                        parent.classList.contains('language-change') ||
                        parent.classList.contains('notifications') ||
                        parent.classList.contains('profile-info') ||
                        parent.classList.contains('navbar') ||
                        parent.classList.contains('navbar-nav') ||
                        parent.classList.contains('navbar-collapse')) {
                        parent.style.setProperty('overflow', 'visible', 'important');
                        parent.style.setProperty('position', 'relative', 'important');
                    }
                    parent = parent.parentElement;
                }
            });
        }
    }

    // Function to handle dropdown show/hide
    function handleDropdownToggle() {
        // Bootstrap dropdown events
        document.addEventListener('show.bs.dropdown', function(e) {
            setTimeout(forceDropdownPositioning, 10);
        });

        document.addEventListener('shown.bs.dropdown', function(e) {
            setTimeout(forceDropdownPositioning, 10);
        });

        // Manual dropdown toggles
        document.addEventListener('click', function(e) {
            if (e.target.matches('.dropdown-toggle, .dropdown-toggleer, .language-btn')) {
                setTimeout(forceDropdownPositioning, 10);
            }
        });

        // MutationObserver to watch for class changes
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const target = mutation.target;
                    if (target.classList.contains('dropdown-menu') ||
                        target.classList.contains('notification-container')) {
                        setTimeout(forceDropdownPositioning, 10);
                    }
                }
            });
        });

        // Observe all dropdown menus
        document.querySelectorAll('.dropdown-menu, .notification-container').forEach(function(dropdown) {
            observer.observe(dropdown, { attributes: true, attributeFilter: ['class'] });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            forceDropdownPositioning();
            handleDropdownToggle();
        });
    } else {
        forceDropdownPositioning();
        handleDropdownToggle();
    }

    // Re-apply on window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            forceDropdownPositioning();
        }, 100);
    });

})();
