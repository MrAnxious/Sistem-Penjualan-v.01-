// File: assets/js/script.js
// JavaScript untuk website toko jenang

// Document ready
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count
    updateCartCount();
    
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            addToCart(productId, 1);
        });
    });
});

// Function to add product to cart
function addToCart(productId, quantity) {
    // Get current cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Check if product already in cart
    const existingItemIndex = cart.findIndex(item => item.id === productId);
    
    if (existingItemIndex !== -1) {
        // Update quantity if product already in cart
        cart[existingItemIndex].quantity += parseInt(quantity);
    } else {
        // Add new product to cart
        cart.push({
            id: productId,
            quantity: parseInt(quantity)
        });
    }
    
    // Save cart to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Show success message
    showSuccessToast('Produk ditambahkan ke keranjang');
    
    // Update cart count
    updateCartCount();
}

// Function to update cart count
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartCount = document.querySelector('.cart-count');
    
    let totalItems = 0;
    cart.forEach(item => {
        totalItems += parseInt(item.quantity);
    });
    
    if (cartCount) {
        cartCount.textContent = totalItems;
    }
}

// Function to clear cart
function clearCart() {
    localStorage.removeItem('cart');
    updateCartCount();
}

// Function to show success toast notification
function showSuccessToast(message) {
    // Check if toastContainer exists, if not create it
    let toastContainer = document.querySelector('.toast-container');
    
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    // Add toast to container
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Initialize and show toast
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
    toast.show();
    
    // Remove toast after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function () {
        toastElement.remove();
    });
}