// swiper
const swiper = new Swiper('.swiper', {
    direction: 'horizontal',
    loop: true,

    pagination: {
        el: '.swiper-pagination',
    },

    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});

// toggle responsive and auth
function toggleMenu() {
    var menu = document.querySelector('ul');
    menu.classList.toggle('active');
}

function toggleAuth(event) {
    event.preventDefault();
    var authForm = document.getElementById('authForm');
    if (authForm.style.right === '0px') {
        authForm.style.right = '-500px';
    } else {
        authForm.style.right = '0px';
    }
}

function closeAuthForm() {
    var authForm = document.getElementById('authForm');
    authForm.style.right = '-500px';
}

$(document).ready(function () {
    $('#signInBtn').click(function () {
        $('#signInForm').show();
        $('#createAccountForm').hide();
    });

    $('#createAccountBtn').click(function () {
        $('#signInForm').hide();
        $('#createAccountForm').show();
    });
});

// ADD TO CART FUNCTION
function addToCart(productName, price) {
    alert(productName + " has been added to your cart!");
    
    // Sample cart logic (for future improvements)
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.push({ name: productName, price: price });
    localStorage.setItem('cart', JSON.stringify(cart));

    // Update cart count (if may cart icon)
    updateCartCount();
}

// UPDATE CART COUNT FUNCTION
function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let cartCountElement = document.querySelector('.fa-shopping-cart span');

    if (cartCountElement) {
        cartCountElement.textContent = `(${cart.length})`;
    }
}

// Run this function para ma-update ang cart count on page load
document.addEventListener("DOMContentLoaded", updateCartCount);
