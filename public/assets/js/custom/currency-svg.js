/**
 * Currency SVG Symbol Handler
 * Replaces text currency symbol with SVG icon for SAR
 */

(function() {
    'use strict';
    
    // SAR Symbol SVG
    const sarSymbolSVG = '<svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-left: 3px;"><g clip-path="url(#clip0_price_5-1)"><path d="M6.68122 10.6309C6.48962 11.0558 6.36297 11.5168 6.31445 12.0003L10.369 11.1384C10.5606 10.7137 10.6872 10.2525 10.7358 9.76904L6.68122 10.6309Z" fill="currentColor"/><path d="M10.3691 8.55619C10.5607 8.13144 10.6873 7.67031 10.7359 7.18683L7.57749 7.85857V6.56725L10.369 5.97403C10.5606 5.54929 10.6873 5.08815 10.7358 4.60467L7.57739 5.27584V0.631863C7.09343 0.903594 6.66363 1.2653 6.31425 1.69195V5.54441L5.05111 5.8129V0.000244141C4.56715 0.27188 4.13735 0.633678 3.78797 1.06033V6.08129L0.961685 6.68186C0.770089 7.1066 0.643345 7.56773 0.594729 8.05122L3.78797 7.3726V8.99879L0.365788 9.72601C0.174192 10.1508 0.0475433 10.6119 -0.000976562 11.0954L3.58109 10.3341C3.87269 10.2735 4.12331 10.1011 4.28625 9.86384L4.94318 8.8899V8.88971C5.01138 8.78895 5.05111 8.66746 5.05111 8.53661V7.10412L6.31425 6.83564V9.41827L10.369 8.55599L10.3691 8.55619Z" fill="currentColor"/></g><defs><clipPath id="clip0_price_5-1"><rect width="10.7368" height="12" fill="white"/></clipPath></defs></svg>';
    
    // Override the original currencyFormat function
    const originalCurrencyFormat = window.currencyFormat;
    
    window.currencyFormat = function(amount, type = "icon", decimals = 2) {
        let symbol = $('#currency_symbol').val();
        let position = $('#currency_position').val();
        let code = $('#currency_code').val();
        
        // Check if currency is SAR and symbol is ^
        const isSAR = code === 'SAR' || symbol === '^';
        
        let formatted_amount = window.formattedAmount ? window.formattedAmount(amount, decimals) : parseFloat(amount).toFixed(decimals);
        
        // Apply currency format based on the position and type
        if (type === "icon" || type === "symbol") {
            if (isSAR) {
                // Use SVG for SAR
                if (position === "right") {
                    return formatted_amount + sarSymbolSVG;
                } else {
                    return sarSymbolSVG + formatted_amount;
                }
            } else {
                // Use text symbol for other currencies
                if (position === "right") {
                    return formatted_amount + symbol;
                } else {
                    return symbol + formatted_amount;
                }
            }
        } else {
            if (position === "right") {
                return formatted_amount + ' ' + code;
            } else {
                return code + ' ' + formatted_amount;
            }
        }
    };
    
    // Replace ^ symbol with SVG in existing elements on page load
    function replaceCurrencySymbols() {
        // Find all elements that might contain currency symbols
        const selectors = [
            '#sub_total',
            '#total_amount',
            '#vat_display',
            '#discount_display',
            '#shipping_display',
            '#rounding_amount',
            '#payable_amount',
            '#modal-order-total',
            '#modal-total-bill',
            '#modal-amount-paid',
            '#modal-due-summary',
            '.product-price',
            '.cart-variant-price',
            '.amount-display',
            '.summary-row span:last-child',
            '.payment-summary-row span:last-child'
        ];
        
        selectors.forEach(selector => {
            $(selector).each(function() {
                let html = $(this).html();
                if (html && html.includes('^')) {
                    $(this).html(html.replace(/\^/g, sarSymbolSVG));
                }
            });
        });
    }
    
    // Run on document ready
    $(document).ready(function() {
        replaceCurrencySymbols();
        
        // Watch for DOM changes and replace symbols
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' || mutation.type === 'characterData') {
                    replaceCurrencySymbols();
                }
            });
        });
        
        // Observe the document body for changes
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            characterData: true
        });
    });
    
})();
