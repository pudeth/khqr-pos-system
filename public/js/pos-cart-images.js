// Override updateCart function to include product images
const originalUpdateCart = window.updateCart;

window.updateCart = function() {
    const cartItemsDiv = document.getElementById('cartItems');
    
    if (cart.length === 0) {
        cartItemsDiv.innerHTML = '<p class="text-gray-500 text-center mt-8">No items in cart</p>';
    } else {
        cartItemsDiv.innerHTML = cart.map((item, index) => {
            const imageHtml = item.image 
                ? `<img src="${item.image.startsWith('http') ? item.image : '/storage/' + item.image}" alt="${item.name}" class="w-16 h-16 object-cover rounded">`
                : `<div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-box text-gray-400"></i></div>`;
            
            return `
            <div class="bg-gray-50 p-3 rounded-lg mb-3 border border-gray-200">
                <div class="flex space-x-3 mb-3">
                    ${imageHtml}
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-sm">${item.name}</h4>
                                <p class="text-sm text-gray-600">$${item.price.toFixed(2)}</p>
                            </div>
                            <button onclick="removeFromCart(${index})" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <button onclick="updateQuantity(${index}, -1)" class="bg-gray-300 w-8 h-8 rounded hover:bg-gray-400">-</button>
                        <span class="w-12 text-center font-bold">${item.quantity}</span>
                        <button onclick="updateQuantity(${index}, 1)" class="bg-gray-300 w-8 h-8 rounded hover:bg-gray-400">+</button>
                    </div>
                    <span class="font-bold text-blue-600">$${(item.price * item.quantity).toFixed(2)}</span>
                </div>
            </div>
        `;
        }).join('');
    }
    
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const tax = 0;
    const discount = 0;
    const total = subtotal + tax - discount;
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax').textContent = '$' + tax.toFixed(2);
    document.getElementById('discount').textContent = '$' + discount.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
};
