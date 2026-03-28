(function ($) {
    "use strict";
    console.log('theme.js execution start', 'jQuery type=', typeof $, 'location=', window.location.href);


    // initialize once DOM is ready
    $(function() {
        sideManu();
    });

    function sideManu() {
        let manuStor = $(".side-bar").html();

        $(".side-bar").html("<div class='overlay'></div>" + manuStor);
        $(".sidebar-opner").on("click ", function () {
            $(".side-bar, .section-container").toggleClass("active");
        });

        // Close button always closes sidebar
        $(".side-bar .close-btn").on("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(".side-bar, .section-container").removeClass("active");
        });

        // Overlay closes sidebar (responsive behavior)
        $(".side-bar .overlay").on("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            if ($(window).width() < 769) {
                $(".side-bar, .section-container").removeClass("active");
            }
        });

        // guarantee every <ul> under an <li> is treated as dropdown
        $("li>ul").addClass("dropdown-menu");

        let animationSpeed = 300;

        let subMenuSelector = ".dropdown-menu";

        // Simple dropdown toggle - same behavior for all screen sizes
        // bind at document level so it always fires even if the menu is rebuilt
        $(document).off("click.sidebarDropdown").on("click.sidebarDropdown", ".side-bar-manu .dropdown > a", function (e) {
            console.log('document capture dropdown click', this);
            e.preventDefault();
            e.stopPropagation();

            let $this = $(this);
            let $parentLi = $this.parent("li");
            // select next UL regardless of class to avoid first-click problems
            let $submenu = $this.next("ul");
            $submenu.addClass("dropdown-menu");

            // If this dropdown has a submenu
            if ($submenu.length > 0) {
                // Toggle this dropdown
                if ($parentLi.hasClass("active")) {
                    console.log('closing dropdown', $this.text().trim());
                    // Close this dropdown
                    $parentLi.removeClass("active");
                    $submenu.removeClass("menu-open");
                    // Hide the submenu
                    $submenu.css('display', 'none');
                } else {
                    console.log('opening dropdown', $this.text().trim());
                    // Close all other dropdowns first
                    $(".side-bar-manu .dropdown.active").each(function() {
                        $(this).removeClass("active");
                        $(this).find(".dropdown-menu").removeClass("menu-open").css('display', 'none');
                    });

                    // Open this dropdown
                    $parentLi.addClass("active");
                    $submenu.addClass("menu-open");

                    // Force show the submenu by overriding inline styles
                    $submenu.css({
                        'display': 'block',
                        'visibility': 'visible',
                        'opacity': '1',
                        'position': 'relative',
                        'inset': 'auto',
                        'transform': 'none',
                        'z-index': 'auto',
                        'float': 'none',
                        'width': 'auto',
                        'min-width': 'auto',
                        'max-width': 'none'
                    });
                }
            }
        });

        // On page load, open any dropdown that is already active (e.g. current route)
        $(".side-bar-manu .dropdown.active").each(function() {
            var $submenu = $(this).find("> .dropdown-menu");
            if ($submenu.length) {
                $submenu.addClass("menu-open").css({
                    'display': 'block',
                    'visibility': 'visible',
                    'opacity': '1',
                    'position': 'relative',
                    'inset': 'auto',
                    'transform': 'none',
                    'z-index': 'auto',
                    'float': 'none',
                    'width': 'auto',
                    'min-width': 'auto',
                    'max-width': 'none'
                });
            }
        });

        // show sidebar in previous menu
        var sidebar = $(".side-bar");

        // Restore scroll position on page load
        var savedScroll = localStorage.getItem("sidebar-scroll");
        if (savedScroll !== null) {
            sidebar.scrollTop(savedScroll);
        }

        // Save scroll position before leaving the page
        $(window).on("beforeunload", function () {
            localStorage.setItem("sidebar-scroll", sidebar.scrollTop());
        });

        // Close sidebar with Escape key
        $(document).on("keydown", function(e) {
            if (e.key === "Escape" && $(".side-bar").hasClass("active")) {
                $(".side-bar, .section-container").removeClass("active");
            }
        });

        // Ensure sidebar can always be closed by clicking outside (for desktop)
        $(document).on("click", function(e) {
            if ($(window).width() >= 769 && $(".side-bar").hasClass("active")) {
                if (!$(e.target).closest(".side-bar, .sidebar-opner").length) {
                    $(".side-bar, .section-container").removeClass("active");
                }
            }
        });

        // Additional mobile dropdown fixes
        $(window).on('resize', function() {
            if ($(window).width() < 769) {
                // Ensure dropdowns work properly after orientation change
                $('.side-bar-manu .dropdown-menu').each(function() {
                    if ($(this).hasClass('menu-open')) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });

        // Add touch-friendly behavior for better mobile experience
        if ('ontouchstart' in window) {
            $('.side-bar-manu .dropdown > a').on('touchstart', function() {
                $(this).addClass('touch-active');
            });

            $('.side-bar-manu .dropdown > a').on('touchend', function() {
                $(this).removeClass('touch-active');
            });
        }

        // Prevent other scripts from hiding dropdown menus with inline styles
        function forceDropdownVisibility() {
            $('.side-bar-manu .dropdown.active .dropdown-menu, .side-bar-manu .dropdown-menu.menu-open').each(function() {
                let $menu = $(this);
                let currentDisplay = $menu.css('display');

                if (currentDisplay === 'none') {
                    // Remove all problematic inline styles and show the dropdown
                    $menu.removeAttr('style');
                    $menu.css({
                        'display': 'block',
                        'visibility': 'visible',
                        'opacity': '1',
                        'position': 'relative'
                    });
                }
            });
        }

        // Watch for style attribute changes using MutationObserver (more efficient than setInterval)
        if (window.MutationObserver) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                        const $target = $(mutation.target);
                        if ($target.hasClass('dropdown-menu') && ($target.closest('.dropdown.active').length || $target.hasClass('menu-open'))) {
                            forceDropdownVisibility();
                        }
                    }
                });
            });

            // Observe all dropdown menus for style changes
            $('.side-bar-manu .dropdown-menu').each(function() {
                observer.observe(this, { attributes: true, attributeFilter: ['style'] });
            });
        }

        // Ensure dropdowns stay open when clicking submenu items
        $('.side-bar-manu .dropdown-menu a').on('click', function(e) {
            // Don't close the dropdown when clicking submenu items
            e.stopPropagation();
        });

        // Prevent dropdown from closing when clicking inside the dropdown menu
        $('.side-bar-manu .dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });
    }

    // photo upload preview
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(".image-preview").attr("src", e.target.result);
                $(".image-preview").hide();
                $(".image-preview").fadeIn(650);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#add-profile").on("change", function () {
        readURL(this);
        $(".image-preview-icon").addClass("d-none");
    });

    $("#feature-btn").on("click", function (e) {
        e.preventDefault();

        let value = $(".add-feature").val();
        let featureCount = $(".feature-list").children().length;

        if (value !== "") {
            $(".feature-list").append(`
            <div class="col-lg-6 mt-4 remove-list">
                <div class="feature-wrp">
                    <div class="form-control d-flex justify-content-between align-items-center">
                        <input name="features[features_${featureCount}][]" required class="border-none" type="text" value="${value}">
                        <label class="switch m-0">
                            <input type="checkbox" checked value="1" name="features[features_${featureCount}][]">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <button type="button" class="remove-one d-none"><i class="fal fa-times"></i></button>
                </div>
            </div>
            `);
            $(".add-feature").val("");
        }
    });
})(jQuery);

document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.querySelector(".menu-opener");
    const sidebarPlan = document.querySelector(".lg-sub-plan");
    const subPlan = document.querySelector(".sub-plan");
    const sidebarAddOn = document.querySelector(".side-bar-addon");
    const sidebarAddOn2 = document.querySelector(".side-bar-addon-2");
    const sidebarAddOn3 = document.querySelector(".side-bar-addon-3");

    toggleBtn.addEventListener("click", function () {
        if (sidebarPlan.style.display === "none") {
            sidebarPlan.style.display = "block";
            subPlan.style.display = "none";
            sidebarAddOn.style.display = "block";
            sidebarAddOn2.style.display = "block";
            sidebarAddOn3.style.display = "block";
        } else {
            sidebarPlan.style.display = "none";
            subPlan.style.display = "block";
            sidebarAddOn.style.display = "none";
            sidebarAddOn2.style.display = "none";
            sidebarAddOn3.style.display = "none";
        }
    });
});

document.querySelector(".sidebar-opner").addEventListener("click", function () {
    const sidebar = document.querySelector(".side-bar-addon");
    if (
        sidebar.style.display === "none" ||
        getComputedStyle(sidebar).display === "none"
    ) {
        sidebar.style.display = "block";
    } else {
        sidebar.style.display = "none";
    }
});

$(document).on("click", "#openUserSignupTab", function (e) {
    e.preventDefault();
    var $otpTabTrigger = $("#otp-tab");
    if ($otpTabTrigger.length) {
        var tab = new bootstrap.Tab($otpTabTrigger[0]);
        tab.show();
    }
});

function cartSelectVariant(selectedBox) {
    const allBoxes = document.querySelectorAll(".cart-variant-box");
    allBoxes.forEach((box) => box.classList.remove("cart-active"));
    selectedBox.classList.add("cart-active");

    const radios = document.querySelectorAll('input[name="variant"]');
    radios.forEach((radio) => (radio.checked = false));
    selectedBox.querySelector('input[type="radio"]').checked = true;
}
