// Tailwind Configuration
tailwind.config = {
    theme: {
        extend: {
            colors: {
               flix: {
                    DEFAULT: '#73D700', // FlixBus Green
                    dark: '#58a300',
                    secondary: '#FFD400', // FlixBus Orange/Yellow
                    darkbg: '#182C50' // FlixBus Dark Blue/Navy
                }
            },
            fontFamily: {
                sans: ['"Hind Siliguri"', 'sans-serif'],
            },
            animation: {
                'fade-in-up': 'fadeInUp 0.3s ease-out'
            },
            keyframes: {
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                }
            }
        }
    }
}

// Application Logic
// State
let cart = [];
let isSidebarOpen = true; // Default to TRUE for desktop logic
let isCartOpen = false;

// Helpers
const isDesktop = () => window.innerWidth >= 1024;

document.addEventListener('DOMContentLoaded', () => {
    // UI Elements
    const overlay = document.getElementById('overlay');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const cartSidebar = document.getElementById('cart-sidebar');
    const cartItemsContainer = document.getElementById('cart-items');
    const emptyCartMsg = document.getElementById('empty-cart-msg');
    const cartTotalEl = document.getElementById('cart-total');
    const cartCountEl = document.getElementById('cart-count');
    const cartCountHeaderEl = document.getElementById('cart-count-header');

    // Initialize Sidebar
    function initSidebar() {
        if (isDesktop()) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('lg:translate-x-0'); // Ensure visible
            mainContent.classList.add('lg:ml-64');
            isSidebarOpen = true;
        } else {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('lg:translate-x-0'); 
            mainContent.classList.remove('lg:ml-64');
            isSidebarOpen = false;
        }
    }
    
    // Toggle Sidebar
    window.toggleSidebar = function() {
        isSidebarOpen = !isSidebarOpen;
        
        if (isDesktop()) {
            if (isSidebarOpen) {
                // OPEN
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('lg:translate-x-0');
                mainContent.classList.add('lg:ml-64');
            } else {
                // CLOSE
                sidebar.classList.remove('lg:translate-x-0');
                sidebar.classList.add('-translate-x-full');
                mainContent.classList.remove('lg:ml-64');
            }
        } else {
            if (isSidebarOpen) {
                sidebar.classList.remove('-translate-x-full');
                if(overlay) {
                    overlay.classList.remove('hidden');
                    setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                }
            } else {
                sidebar.classList.add('-translate-x-full');
                closeOverlay();
            }
        }
    }

    // Toggle Cart
    window.toggleCart = function() {
        isCartOpen = !isCartOpen;
        if (isCartOpen) {
            cartSidebar.classList.remove('translate-x-full');
            if(overlay) {
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            }
        } else {
            cartSidebar.classList.add('translate-x-full');
            if (!isSidebarOpen || isDesktop()) closeOverlay();
        }
    }

    window.closeAllDrawers = function() {
        if (isCartOpen) toggleCart();
        if (!isDesktop() && isSidebarOpen) toggleSidebar();
    }

    function closeOverlay() {
        if(overlay) {
            const shouldKeepOverlay = isCartOpen || (!isDesktop() && isSidebarOpen);
            
            if (!shouldKeepOverlay) {
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }
    }

    // Cart Functions
    window.addToCart = function(name, price) {
        const existingItem = cart.find(item => item.name === name);

        if (existingItem) {
            existingItem.qty++;
        } else {
            cart.push({ name, price, qty: 1, id: Date.now() });
        }
        
        updateCartUI();
        showToast(`${name} কার্টে যোগ হয়েছে!`);
    }

    window.updateQty = function(id, change) {
        const item = cart.find(i => i.id === id);
        if (!item) return;

        item.qty += change;
        if (item.qty <= 0) {
            removeFromCart(id);
        } else {
            updateCartUI();
        }
    }

    window.removeFromCart = function(id) {
        cart = cart.filter(item => item.id !== id);
        updateCartUI();
    }

    function updateCartUI() {
        const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
        const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);

        if(cartCountEl) cartCountEl.innerText = totalQty;
        if(cartCountHeaderEl) cartCountHeaderEl.innerText = totalQty;
        if(cartTotalEl) cartTotalEl.innerText = '৳ ' + totalPrice.toLocaleString();

        localStorage.setItem('flixCart', JSON.stringify(cart));

        if (cart.length === 0) {
            if(emptyCartMsg) emptyCartMsg.classList.remove('hidden');
            if(cartItemsContainer) {
                cartItemsContainer.innerHTML = '';
                cartItemsContainer.appendChild(emptyCartMsg);
            }
        } else {
            if(emptyCartMsg) emptyCartMsg.classList.add('hidden');
            if(cartItemsContainer) {
                cartItemsContainer.innerHTML = cart.map(item => `
                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-100 animate-fade-in-up">
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800 text-sm truncate">${item.name}</h4>
                            <span class="text-flix font-semibold text-sm">৳ ${item.price.toLocaleString()}</span>
                        </div>
                        
                        <div class="flex items-center gap-3 bg-white border border-gray-200 rounded px-2 py-1 mx-2">
                            <button onclick="updateQty(${item.id}, -1)" class="text-gray-500 hover:text-flix transition text-sm"><i class="fas fa-minus"></i></button>
                            <span class="font-bold text-gray-800 w-4 text-center text-sm">${item.qty}</span>
                            <button onclick="updateQty(${item.id}, 1)" class="text-gray-500 hover:text-flix transition text-sm"><i class="fas fa-plus"></i></button>
                        </div>

                        <button onclick="removeFromCart(${item.id})" class="text-red-400 hover:text-red-600 p-2 transition">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `).join('');
            }
        }
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.innerHTML = `<i class="fas fa-check-circle mr-2"></i> ${message}`;
        toast.className = "fixed bottom-5 right-5 bg-gray-900 text-white px-6 py-3 rounded-lg shadow-2xl z-50 transition-all duration-300 transform translate-y-20 flex items-center";
        document.body.appendChild(toast);

        setTimeout(() => toast.classList.remove("translate-y-20"), 100);
        setTimeout(() => {
            toast.classList.add("translate-y-20", "opacity-0");
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Initialize logic
    initSidebar();
    
    // Load Cart
    const savedCart = localStorage.getItem('flixCart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        updateCartUI();
    }
});

// Slider Logic
let slideIndex = 0;
window.changeSlide = function(n) {
    showSlides(slideIndex += n);
}
window.currentSlide = function(n) {
    showSlides(slideIndex = n);
}

function showSlides(n) {
    const slides = document.getElementsByClassName("slide");
    const dots = document.getElementsByClassName("dot");
    if(slides.length === 0) return;

    if (n >= slides.length) {slideIndex = 0}
    if (n < 0) {slideIndex = slides.length - 1}
    
    for (let i = 0; i < slides.length; i++) {
        slides[i].classList.remove("active");
        if(dots[i]) {
            dots[i].classList.remove("bg-flix", "w-8");
            dots[i].classList.add("bg-white");
        }
    }
    
    slides[slideIndex].classList.add("active");
    if(dots[slideIndex]) {
        dots[slideIndex].classList.remove("bg-white");
        dots[slideIndex].classList.add("bg-flix", "w-8"); 
    }
}

// Auto slide
setInterval(() => {
    window.changeSlide(1);
}, 5000);
