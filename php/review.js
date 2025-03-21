document.addEventListener("DOMContentLoaded", function () {
    const reviewButtons = document.querySelectorAll(".review-btn");
    const reviewModal = document.getElementById("review-modal");
    const closeModal = document.querySelector(".close");
    const reviewForm = document.getElementById("review-form");
    const orderIdField = document.getElementById("order-id");
    const productIdField = document.getElementById("product-id");

    const stars = document.querySelectorAll('.stars span');
    const ratingInput = document.getElementById('rating');
    const ratingText = document.querySelector('.rating-text');

    console.log("Review script loaded!");

    if (reviewButtons.length === 0 || !reviewModal || !closeModal || !reviewForm) {
        console.error("Error: Review elements not found in DOM.");
        return;
    }

    stars.forEach(star => {
        star.addEventListener('click', function () {
            const value = parseInt(this.getAttribute('data-value'));
            ratingInput.value = value;

            stars.forEach((s, index) => {
                s.textContent = index < value ? '★' : '☆';
                s.classList.toggle('active', index < value);
            });

            ratingText.textContent = `${value} Star${value !== 1 ? 's' : ''}`;
        });

        star.addEventListener('mouseover', function () {
            const value = parseInt(this.getAttribute('data-value'));
            stars.forEach((s, index) => {
                s.textContent = index < value ? '★' : '☆';
            });
        });

        star.addEventListener('mouseout', function () {
            const currentValue = ratingInput.value || 0;
            stars.forEach((s, index) => {
                s.textContent = index < currentValue ? '★' : '☆';
            });
        });
    });

    reviewButtons.forEach(button => {
        button.addEventListener("click", function () {
            console.log("Review button clicked:", this.getAttribute("data-product-id"));
            orderIdField.value = this.getAttribute("data-order");
            productIdField.value = this.getAttribute("data-product-id");
            reviewModal.style.display = "block";

            stars.forEach(s => {
                s.textContent = '☆';
                s.classList.remove('active');
            });
            ratingInput.value = '';
            ratingText.textContent = 'Click to rate';
        });
    });

    closeModal.addEventListener("click", function () {
        reviewModal.style.display = "none";
    });

    window.addEventListener("click", function (event) {
        if (event.target === reviewModal) {
            reviewModal.style.display = "none";
        }
    });

    reviewForm.addEventListener("submit", function (event) {
        event.preventDefault();

        if (!ratingInput.value) {
            alert('Please select a rating!');
            return;
        }

        const formData = new FormData(reviewForm);

        fetch("submit_review.php", {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                reviewModal.style.display = "none";
                reviewForm.reset();

                stars.forEach(s => {
                    s.textContent = '☆';
                    s.classList.remove('active');
                });
                ratingText.textContent = 'Click to rate';
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred. Please try again.");
            });
    });
});