/**
 * Clean SAR Symbol SVG Display
 * This script finds and properly renders SAR SVG symbols that appear as text
 */

(function() {
    'use strict';

    /**
     * Clean SVG text and render properly
     */
    function cleanSARSVG() {
        // Find all elements that might contain SVG text
        const elements = document.querySelectorAll('*');
        
        elements.forEach(element => {
            // Skip script, style, and noscript tags
            const tagName = element.tagName;
            if (tagName === 'SCRIPT' || tagName === 'STYLE' || tagName === 'NOSCRIPT' || tagName === 'svg' || tagName === 'SVG') {
                return;
            }

            // Check if element contains SVG text
            const text = element.innerHTML;
            if (text && text.includes('sar-symbol-svg') && text.includes('<svg')) {
                // Already has proper SVG, skip
                if (element.querySelector('svg.sar-symbol-svg')) {
                    return;
                }

                // Check if it's escaped HTML
                if (text.includes('&lt;svg') || text.includes('&lt;path')) {
                    // Decode HTML entities
                    const decoded = text
                        .replace(/&lt;/g, '<')
                        .replace(/&gt;/g, '>')
                        .replace(/&quot;/g, '"')
                        .replace(/&#039;/g, "'")
                        .replace(/&amp;/g, '&');
                    
                    element.innerHTML = decoded;
                }
            }
        });
    }

    /**
     * Observe DOM changes
     */
    function observeDOM() {
        const observer = new MutationObserver(function(mutations) {
            let shouldClean = false;
            
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        const text = node.textContent || '';
                        if (text.includes('sar-symbol-svg') || text.includes('&lt;svg')) {
                            shouldClean = true;
                        }
                    }
                });
            });

            if (shouldClean) {
                cleanSARSVG();
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            cleanSARSVG();
            observeDOM();
        });
    } else {
        cleanSARSVG();
        observeDOM();
    }

    // Run multiple times to catch late-loading content
    setTimeout(cleanSARSVG, 100);
    setTimeout(cleanSARSVG, 500);
    setTimeout(cleanSARSVG, 1000);
    setTimeout(cleanSARSVG, 2000);

    // Make function globally available
    window.cleanSARSVG = cleanSARSVG;

    // Also run when cart is updated
    if (typeof $ !== 'undefined') {
        $(document).on('cart:updated', cleanSARSVG);
    }

})();
