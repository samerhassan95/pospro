/**
 * Replace SAR Symbol (^) with SVG Icon
 * This script runs after page load to replace any remaining ^ symbols
 */

(function() {
    'use strict';

    // SAR Symbol SVG
    const sarSymbolSVG = '<svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin: 0 3px;"><g clip-path="url(#clip0_price_5-1)"><path d="M6.68122 10.6309C6.48962 11.0558 6.36297 11.5168 6.31445 12.0003L10.369 11.1384C10.5606 10.7137 10.6872 10.2525 10.7358 9.76904L6.68122 10.6309Z" fill="currentColor"/><path d="M10.3691 8.55619C10.5607 8.13144 10.6873 7.67031 10.7359 7.18683L7.57749 7.85857V6.56725L10.369 5.97403C10.5606 5.54929 10.6873 5.08815 10.7358 4.60467L7.57739 5.27584V0.631863C7.09343 0.903594 6.66363 1.2653 6.31425 1.69195V5.54441L5.05111 5.8129V0.000244141C4.56715 0.27188 4.13735 0.633678 3.78797 1.06033V6.08129L0.961685 6.68186C0.770089 7.1066 0.643345 7.56773 0.594729 8.05122L3.78797 7.3726V8.99879L0.365788 9.72601C0.174192 10.1508 0.0475433 10.6119 -0.000976562 11.0954L3.58109 10.3341C3.87269 10.2735 4.12331 10.1011 4.28625 9.86384L4.94318 8.8899V8.88971C5.01138 8.78895 5.05111 8.66746 5.05111 8.53661V7.10412L6.31425 6.83564V9.41827L10.369 8.55599L10.3691 8.55619Z" fill="currentColor"/></g><defs><clipPath id="clip0_price_5-1"><rect width="10.7368" height="12" fill="white"/></clipPath></defs></svg>';

    /**
     * Check if currency is SAR
     */
    function isSARCurrency() {
        const currencyCode = document.getElementById('currency_code')?.value;
        const currencySymbol = document.getElementById('currency_symbol')?.value;
        return currencyCode === 'SAR' || currencySymbol === '^';
    }

    /**
     * Replace ^ symbol with SVG in element
     */
    function replaceInElement(element) {
        if (!element || !element.textContent) return;
        
        // Skip script, style, and noscript tags
        const tagName = element.tagName;
        if (tagName === 'SCRIPT' || tagName === 'STYLE' || tagName === 'NOSCRIPT') {
            return;
        }

        // Get all text nodes
        const walker = document.createTreeWalker(
            element,
            NodeFilter.SHOW_TEXT,
            null
        );

        const nodesToReplace = [];
        let node;
        
        while (node = walker.nextNode()) {
            // Skip if parent is script or style
            const parent = node.parentElement;
            if (parent && (parent.tagName === 'SCRIPT' || parent.tagName === 'STYLE' || parent.tagName === 'NOSCRIPT')) {
                continue;
            }
            
            // Check if contains ^
            if (node.textContent && node.textContent.includes('^')) {
                nodesToReplace.push(node);
            }
        }

        // Replace in collected nodes
        nodesToReplace.forEach(textNode => {
            const parent = textNode.parentElement;
            if (parent) {
                const text = textNode.textContent;
                // Only replace if ^ is surrounded by numbers or spaces (currency context)
                if (/[\d\s]/.test(text)) {
                    const newHTML = text.replace(/\^/g, sarSymbolSVG);
                    const span = document.createElement('span');
                    span.innerHTML = newHTML;
                    
                    // Replace all child nodes
                    while (span.firstChild) {
                        parent.insertBefore(span.firstChild, textNode);
                    }
                    parent.removeChild(textNode);
                }
            }
        });
    }

    /**
     * Replace ^ in entire document
     */
    function replaceSARSymbol() {
        if (!isSARCurrency()) {
            return;
        }

        console.log('🔄 Replacing SAR symbols...');
        replaceInElement(document.body);
        console.log('✅ SAR symbols replaced');
    }

    /**
     * Observe DOM changes
     */
    function observeDOM() {
        if (!isSARCurrency()) {
            return;
        }

        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        replaceInElement(node);
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Run immediately if DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            replaceSARSymbol();
            observeDOM();
        });
    } else {
        replaceSARSymbol();
        observeDOM();
    }

    // Run multiple times to catch late-loading content
    setTimeout(replaceSARSymbol, 100);
    setTimeout(replaceSARSymbol, 500);
    setTimeout(replaceSARSymbol, 1000);
    setTimeout(replaceSARSymbol, 2000);
    setTimeout(replaceSARSymbol, 3000);

    // Make function globally available
    window.replaceSARSymbol = replaceSARSymbol;

})();
