/**
 * Dynamically apply primary color to sidebar SVG images
 * This script converts the primary color to a CSS filter
 */

(function() {
    'use strict';

    // Function to convert hex color to CSS filter
    function hexToFilter(hex) {
        // Remove # if present
        hex = hex.replace('#', '');
        
        // Convert hex to RGB
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        
        // For dark blue #011646, we use a pre-calculated filter
        if (hex.toLowerCase() === '011646') {
            return 'brightness(0) saturate(100%) invert(7%) sepia(98%) saturate(4299%) hue-rotate(211deg) brightness(94%) contrast(105%)';
        }
        
        // For other colors, use a simpler approximation
        // This converts any color to grayscale then tints it
        const brightness = (r * 0.299 + g * 0.587 + b * 0.114) / 255;
        const hue = rgbToHue(r, g, b);
        const saturation = rgbToSaturation(r, g, b);
        
        return `brightness(0) saturate(100%) invert(${brightness * 100}%) sepia(100%) saturate(${saturation * 100}%) hue-rotate(${hue}deg)`;
    }
    
    function rgbToHue(r, g, b) {
        r /= 255;
        g /= 255;
        b /= 255;
        const max = Math.max(r, g, b);
        const min = Math.min(r, g, b);
        let h = 0;
        
        if (max !== min) {
            const d = max - min;
            switch (max) {
                case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
                case g: h = ((b - r) / d + 2) / 6; break;
                case b: h = ((r - g) / d + 4) / 6; break;
            }
        }
        return Math.round(h * 360);
    }
    
    function rgbToSaturation(r, g, b) {
        r /= 255;
        g /= 255;
        b /= 255;
        const max = Math.max(r, g, b);
        const min = Math.min(r, g, b);
        const l = (max + min) / 2;
        
        if (max === min) {
            return 0;
        }
        
        const d = max - min;
        return l > 0.5 ? d / (2 - max - min) : d / (max + min);
    }

    // Get primary color from CSS variable
    function getPrimaryColor() {
        const root = document.documentElement;
        const primaryColor = getComputedStyle(root).getPropertyValue('--clr-primary').trim();
        return primaryColor;
    }

    // Apply filter to sidebar images
    function applySidebarIconColors() {
        const primaryColor = getPrimaryColor();
        
        if (!primaryColor || primaryColor === '') {
            console.warn('Primary color not found');
            return;
        }
        
        const filter = hexToFilter(primaryColor);
        
        // Create or update style element
        let styleEl = document.getElementById('sidebar-icon-dynamic-style');
        if (!styleEl) {
            styleEl = document.createElement('style');
            styleEl.id = 'sidebar-icon-dynamic-style';
            document.head.appendChild(styleEl);
        }
        
        // Apply the filter for all sidebar icons
        styleEl.textContent = `
            /* Default state - use primary color */
            .side-bar-manu li a .sidebar-icon img {
                filter: ${filter} !important;
            }
            
            /* Active and hover states - white color */
            .side-bar-manu li.active > a .sidebar-icon img,
            .side-bar-manu li:hover > a .sidebar-icon img {
                filter: brightness(0) invert(1) !important;
            }
            
            /* RTL Support */
            [dir="rtl"] .side-bar-manu li a .sidebar-icon img,
            html[dir="rtl"] body .side-bar-manu li a .sidebar-icon img {
                filter: ${filter} !important;
            }
            
            [dir="rtl"] .side-bar-manu li.active > a .sidebar-icon img,
            [dir="rtl"] .side-bar-manu li:hover > a .sidebar-icon img,
            html[dir="rtl"] body .side-bar-manu li.active > a .sidebar-icon img,
            html[dir="rtl"] body .side-bar-manu li:hover > a .sidebar-icon img {
                filter: brightness(0) invert(1) !important;
            }
        `;
        
        console.log('Sidebar icons updated with color:', primaryColor);
    }

    // Run on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applySidebarIconColors);
    } else {
        applySidebarIconColors();
    }

    // Re-apply when CSS variables change
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'style') {
                applySidebarIconColors();
            }
        });
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['style']
    });

})();
