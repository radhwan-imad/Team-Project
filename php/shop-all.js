document.addEventListener('DOMContentLoaded', () => {
    const cartSidebar = document.getElementById('cart-sidebar');
    const cartContent = document.getElementById('cart-content');
    const cartTotal = document.getElementById('cart-total');
    const overlay = document.getElementById('overlay');
    const closeCart = document.getElementById('close-cart');
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');

    const toggleCart = (show) => {
        if (show) {
            cartSidebar.classList.add('active');
            overlay.classList.add('active');
        } else {
            cartSidebar.classList.remove('active');
            overlay.classList.remove('active');
        }
    };

    const loadCart = () => {
        fetch('cart.php', {
            method: 'GET',
        })
            .then(response => response.json())
            .then(data => {
                cartContent.innerHTML = '';
                if (data.cart.length === 0) {
                    cartContent.innerHTML = '<p>Your cart is empty.</p>';
                } else {
                    data.cart.forEach(item => {
                        cartContent.innerHTML += `
                            <div class="cart-item">
                                <img src="images/${item.image}" alt="${item.name}" style="width: 50px;">
                                <div>
                                    <p>${item.name}</p>
                                    <p>£${(item.price * item.quantity).toFixed(2)}</p>
                                    <p>Qty: ${item.quantity}</p>
                                </div>
                                <button class="remove-from-cart" data-id="${item.id}">Remove</button>
                            </div>
                        `;
                    });
                }
                cartTotal.textContent = data.total.toFixed(2);
            });
    };

    addToCartButtons.forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.dataset.id;
            const productName = button.dataset.name;
            const productPrice = button.dataset.price;
            const productImage = button.dataset.image;

            fetch('cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'add',
                    product_id: productId,
                    product_name: productName,
                    product_price: productPrice,
                    product_image: productImage,
                }),
            }).then(() => {
                loadCart();
                toggleCart(true);
            });
        });
    });

    closeCart.addEventListener('click', () => toggleCart(false));
    overlay.addEventListener('click', () => toggleCart(false));

    loadCart();
});
