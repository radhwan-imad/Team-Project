document.addEventListener('DOMContentLoaded', function() {
    // Card display elements
    const cardNumber = document.getElementById("number");
    const numberInp = document.getElementById("card_number");
    const nameInp = document.getElementById("card_name");
    const cardName = document.getElementById("name");
    const cardMonth = document.getElementById("month");
    const cardYear = document.getElementById("year");
    const monthInp = document.getElementById("card_month");
    const yearInp = document.getElementById("card_year");
    const cardCvc = document.getElementById("cvc");
    const cvcInp = document.getElementById("card_cvc");
    
    // Form and submit elements
    const form = document.getElementById("payment-form");
    const submitBtn = document.getElementById("submit_btn");
    const thankYouSection = document.querySelector(".thank");
    const receiptDetails = document.getElementById("receipt-details");
    const continueBtn = document.getElementById("continue-btn");

    // Card display functions
    function updateCardDisplay(input, display, defaultText = '') {
        display.textContent = input.value || defaultText;
    }

    function formatCardNumber(value) {
        // Remove non-digit characters
        const cleanValue = value.replace(/\D/g, '');
        // Add space every 4 digits
        return cleanValue.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
    }

    // Event listeners for card display
    numberInp.addEventListener("input", function() {
        this.value = formatCardNumber(this.value);
        updateCardDisplay(this, cardNumber, "0000 0000 0000 0000");
    });

    nameInp.addEventListener("input", function() {
        updateCardDisplay(this, cardName, "Jane Appleseed");
    });

    monthInp.addEventListener("input", function() {
        updateCardDisplay(this, cardMonth, "00");
    });

    yearInp.addEventListener("input", function() {
        updateCardDisplay(this, cardYear, "00");
    });

    cvcInp.addEventListener("input", function() {
        updateCardDisplay(this, cardCvc, "000");
    });

    // Validation functions
    function validateField(input, validationFn, errorMessage) {
        const errorSpan = input.nextElementSibling;
        const isValid = validationFn(input.value);
        
        if (!isValid) {
            input.classList.add('error');
            errorSpan.textContent = errorMessage;
        } else {
            input.classList.remove('error');
            errorSpan.textContent = '';
        }
        
        return isValid;
    }

    function validateForm() {
        const validations = [
            {
                input: nameInp,
                validate: (value) => value.trim().length >= 2,
                message: 'Name must be at least 2 characters'
            },
            {
                input: numberInp,
                validate: (value) => /^\d{4}\s\d{4}\s\d{4}\s\d{4}$/.test(value),
                message: 'Invalid card number'
            },
            {
                input: monthInp,
                validate: (value) => /^(0[1-9]|1[0-2])$/.test(value),
                message: 'Invalid month'
            },
            {
                input: yearInp,
                validate: (value) => /^(2[3-9]|3[0-5])$/.test(value),
                message: 'Invalid year'
            },
            {
                input: cvcInp,
                validate: (value) => /^\d{3}$/.test(value),
                message: 'Invalid CVC'
            }
        ];

        return validations.every(({ input, validate, message }) => 
            validateField(input, validate, message)
        );
    }

    // Form submission handler
    function handleSubmit(e) {
        e.preventDefault();
        
        // Validate form
        if (!validateForm()) {
            console.log('Validation failed');
            return;
        }

        // Prepare form data
        const formData = new FormData(form);

        // Send AJAX request
        fetch('payment.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(result => {
            console.log('Payment result:', result);
            
            if (result.status === 'success') {
                // Display receipt details
                const receiptHTML = `
                    <p>Booking ID: ${result.booking_id}</p>
                    <p>Amount: $${result.transaction_details.amount.toFixed(2)}</p>
                    <p>Card: ${result.transaction_details.card_number}</p>
                    <p>Date: ${result.transaction_details.date}</p>
                `;
                receiptDetails.innerHTML = receiptHTML;

                // Show thank you section
                form.classList.add('hidden');
                thankYouSection.classList.remove('hidden');
            } else {
                // Show error message
                alert(result.message || 'Payment processing failed');
            }
        })
        .catch(error => {
            console.error('Payment error:', error);
            alert(`An error occurred: ${error.message}`);
        });
    }

    // Continue button handler
    if (continueBtn) {
        continueBtn.addEventListener('click', () => {
            // Redirect to appropriate page
            window.location.href = 'index.php'; // Change as needed
        });
    }

    // Add form submission event listener
    form.addEventListener('submit', handleSubmit);
});