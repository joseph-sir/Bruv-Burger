// public/js/validation.js

document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Checkout Form Validation
    const checkoutForm = document.getElementById('checkoutForm');
    
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            let isValid = true;
            let errorMessage = "";

            // Get form values
            const firstName = document.getElementById('first_name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            
            // Clear previous errors
            document.getElementById('jsErrorBox').style.display = 'none';

            // Validate First Name
            if (firstName.length < 2) {
                isValid = false;
                errorMessage += "• First name must be at least 2 characters long.\n";
            }

            // Validate Email format using Regex
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                isValid = false;
                errorMessage += "• Please enter a valid email address.\n";
            }

            // Validate South African Phone Number (Must be 10 digits, starting with 0)
            const phoneRegex = /^0[0-9]{9}$/;
            if (!phoneRegex.test(phone)) {
                isValid = false;
                errorMessage += "• Please enter a valid 10-digit SA phone number (e.g., 0821234567).\n";
            }

            // If validation fails, stop the form from submitting
            if (!isValid) {
                e.preventDefault(); // Stops form submission
                const errorBox = document.getElementById('jsErrorBox');
                errorBox.innerText = "Please fix the following errors:\n\n" + errorMessage;
                errorBox.style.display = 'block';
                window.scrollTo(0, 0); // Scroll to top to see the error
            } else {
                // If valid, disable the submit button to prevent double-clicks
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerText = "Processing...";
            }
        });
    }
});