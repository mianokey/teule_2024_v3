jQuery(
    (function ($) {
        "use strict";

        $(window).on("scroll", function () {
            if ($(this).scrollTop() > 50) {
                $(".main-nav").addClass("menu-shrink");
            } else {
                $(".main-nav").removeClass("menu-shrink");
            }
        });

        $("#close-btn").on("click", function () {
            $("#search-overlay").fadeOut();
            $("#search-btn").show();
        });

        $("#search-btn").on("click", function () {
            $("#search-overlay").fadeIn();
        });

        jQuery(".mean-menu").meanmenu({ meanScreenWidth: "991" });

        $("select").niceSelect();

        $(".js-modal-btn").modalVideo();

        new WOW().init();

        lightbox.option({
            resizeDuration: 200,
            wrapAround: true,
            fadeDuration: 100,
            imageFadeDuration: 200,
            disableScrolling: true,
            positionFromTop: 120,
        });

        jQuery(window).on("load", function () {
            jQuery(".loader").fadeOut(500);
        });

        $(".banner-slider").owlCarousel({
            items: 1,
            loop: true,
            margin: 0,
            nav: true,
            dots: false,
            smartSpeed: 1000,
            autoplay: true,
            autoplayTimeout: 4000,
            autoplayHoverPause: true,
            animateOut: "fadeOut",
            animateIn: "fadeIn",
            navText: [
                "<i class='icofont-rounded-double-left'></i>",
                "<i class='icofont-rounded-double-right'></i>",
            ],
        });

        $(".gallery-slider").owlCarousel({
            items: 1,
            loop: true,
            margin: 20,
            nav: false,
            dots: false,
            smartSpeed: 1000,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            responsive: {
                0: { items: 1 },
                600: { items: 3 },
                1000: { items: 5 },
            },
        });

        $(".odometer").appear(function (e) {
            var odo = $(".odometer");
            odo.each(function () {
                var countNumber = $(this).attr("data-count");
                $(this).html(countNumber);
            });
        });

        $(".testimonial-slider").owlCarousel({
            items: 1,
            loop: true,
            margin: 20,
            nav: false,
            dots: true,
            smartSpeed: 1000,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
        });
        $(".home-slider").owlCarousel({
            items: 1,
            loop: true,
            margin: 20,
            nav: false,
            dots: true,
            smartSpeed: 1000,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
        });

        $(".accordion > li:eq(0) a").addClass("active").next().slideDown();
        $(".accordion a").on("click", function (j) {
            var dropDown = $(this).closest("li").find("p");
            $(this).closest(".accordion").find("p").not(dropDown).slideUp();

            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
            } else {
                $(this)
                    .closest(".accordion")
                    .find("a.active")
                    .removeClass("active");
                $(this).addClass("active");
            }

            dropDown.stop(false, true).slideToggle();
            j.preventDefault();
        });

        let getDaysId = document.getElementById("days");
        if (getDaysId !== null) {
            const second = 1000;
            const minute = second * 60;
            const hour = minute * 60;
            const day = hour * 24;
            let countDown = new Date("December 25, 2025 00:00:00").getTime();

            setInterval(function () {
                let now = new Date().getTime();
                let distance = countDown - now;

                document.getElementById("days").innerText = Math.floor(
                    distance / day
                );
                document.getElementById("hours").innerText = Math.floor(
                    (distance % day) / hour
                );
                document.getElementById("minutes").innerText = Math.floor(
                    (distance % hour) / minute
                );
                document.getElementById("seconds").innerText = Math.floor(
                    (distance % minute) / second
                );
            }, second);
        }

        $(function () {
            $(window).on("scroll", function () {
                var scrolled = $(window).scrollTop();

                if (scrolled > 500) $(".go-top").addClass("active");

                if (scrolled < 500) $(".go-top").removeClass("active");
            });

            $(".go-top").on("click", function () {
                $("html, body").animate({ scrollTop: "0" }, 500);
            });
        });

        $(".newsletter-form")
            .validator()
            .on("submit", function (event) {
                if (event.isDefaultPrevented()) {
                    formErrorSub();
                    submitMSGSub(false, "Please enter your email correctly.");
                } else {
                    event.preventDefault();
                }
            });

        function callbackFunction(resp) {
            if (resp.result === "success") {
                formSuccessSub();
            } else {
                formErrorSub();
            }
        }

        function formSuccessSub() {
            $(".newsletter-form")[0].reset();
            submitMSGSub(true, "Thank you for subscribing!");
            setTimeout(function () {
                $("#validator-newsletter").addClass("hide");
            }, 4000);
        }

        function formErrorSub() {
            $(".newsletter-form").addClass("animated shake");
            setTimeout(function () {
                $(".newsletter-form").removeClass("animated shake");
            }, 1000);
        }

        function submitMSGSub(valid, msg) {
            if (valid) {
                var msgClasses = "validation-success";
            } else {
                var msgClasses = "validation-danger";
            }
            $("#validator-newsletter")
                .removeClass()
                .addClass(msgClasses)
                .text(msg);
        }

        $(".newsletter-form").ajaxChimp({
            url: "https://hibootstrap.us20.list-manage.com/subscribe/post?u=60e1ffe2e8a68ce1204cd39a5&amp;id=42d6d188d9",
            callback: callbackFunction,
        });

        $("body").append(
            "<div class='switch-box'><label id='switch' class='switch'><input type='checkbox' onchange='toggleTheme()' id='slider'><span class='slider round'></span></label></div>"
        );
    })(jQuery)
);

function setTheme(themeName) {
    localStorage.setItem("findo_theme", themeName);
    document.documentElement.className = themeName;
}

function toggleTheme() {
    if (localStorage.getItem("findo_theme") === "theme-dark") {
        setTheme("theme-light");
    } else {
        setTheme("theme-dark");
    }
}

(function () {
    if (localStorage.getItem("findo_theme") === "theme-dark") {
        setTheme("theme-dark");
        document.getElementById("slider").checked = false;
    } else {
        setTheme("theme-light");
        document.getElementById("slider").checked = true;
    }
})();

$(document).ready(function () {
    // Get the link with ID "open-in-new-tab-link"
    const openInNewTabLink = document.getElementById("open-in-new-tab-link");

    // Add a click event listener to the link
    openInNewTabLink.addEventListener("click", (event) => {
        event.preventDefault(); // Prevent the link from opening in the same tab
        window.open(openInNewTabLink.href, "_blank", "noopener"); // Open the link in a new tab
    });

    $(".counter-value").each(function () {
        $(this)
            .prop("Counter", 0)
            .animate(
                {
                    Counter: $(this).text(),
                },
                {
                    duration: 3500,
                    easing: "swing",
                    step: function (now) {
                        $(this).text(Math.ceil(now));
                    },
                }
            );
    });
});

$(document).ready(function() {
    $("#sposn-fancybox").fancybox({
        type: "inline", // Specify that the content is HTML
        touch: false,// Disable touch gestures for better popover experience on desktop
        arrows: true, 
        buttons: [
            "zoom",
            "share",
            "slideShow",
            "fullScreen",
            "download",
            "thumbs",
            "close"
        ]
    });
});


// Function to show the loader
function showLoader() {
    document.getElementById("loader").style.display = "block";
}

// Function to hide the loader
function hideLoader() {
    document.getElementById("loader").style.display = "none";
}

// Function to handle form submission
function submitForm(formElement, method) {
    // Show the loader before submitting the form
    showLoader();

    // Get the URL from the form's action attribute
    const formUrl = formElement.action;

    // Get formdata
    const formData = new FormData(formElement);

    // Get the file input element that allows multiple files
    const fileInput = document.getElementById('fileInput');

    // Check if the file input has files selected
    if (fileInput && fileInput.files.length > 0) {
        // Loop through the selected files and append them to FormData
        for (let i = 0; i < fileInput.files.length; i++) {
            formData.append('attachments[]', fileInput.files[i]);
        }
    }

    // Submit the form using JavaScript's fetch API or any other AJAX method
    fetch(formUrl, {
        method: method,
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            // Check if the response contains a success message
            if (data && data.message) {
                // Show the success message to the user

                // Redirect to the URL from the response data (if any)
                const { redirect_url } = data;
                if (redirect_url) {
                    window.location.href = redirect_url;
                }
            } else {
                // If the response does not have a message, handle the error case
                console.error('Unexpected response:', data);
            }
        })
        .catch((error) => {
            // Handle any errors that occurred during the fetch request
            console.error('Error during form submission:', error);
        });
}


// Event listener to handle form submission for all forms with class "ajax-form"
document.querySelectorAll(".ajax-form").forEach((form) => {
    form.addEventListener("submit", function (event) {
        event.preventDefault();
        const formMethod = this.method.toUpperCase(); // Get the form method in uppercase
        submitForm(this, formMethod); // Pass the form element and method to the submitForm function
    });
});
document.addEventListener("DOMContentLoaded", function () {
    // Get all <img> tags
    var images = document.querySelectorAll("img");

    // Add the 'loading="lazy"' attribute to each <img> tag
    images.forEach(function (img) {
        img.setAttribute("loading", "lazy");
    });
});

window.onload = function () {
    const wrapper = document.querySelector('.scrolling-text-wrapper');
    const separator = ' ♥ '; // Customize separator
  
    // Automatically append the separator between <p> elements
    const paragraphs = wrapper.querySelectorAll('p');
    paragraphs.forEach((p, index) => {
      if (index < paragraphs.length - 1) {
        const separatorSpan = document.createElement('span');
        separatorSpan.textContent = separator;
        separatorSpan.style.color = 'red'; // Style the separator
        p.appendChild(separatorSpan);
      }
    });
  };


  $(document).ready(function(){
    $(".impact-carousel").owlCarousel({
      loop: true,             // Infinite looping
      margin: 10,             // Space between items
      nav: false,             // Disable arrows
      dots: true,             // Enable dots
      autoplay: true,         // Enable autoplay
      autoplayTimeout: 3000,  // Time between slides (ms)
      items: 1                // Display one item at a time
    });
  });

  
  
