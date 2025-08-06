document.addEventListener('DOMContentLoaded', function() {
    initSearch();
    initCart();
    initProductFilters();
});

function initSearch() {
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');
    
    if (searchInput && searchBtn) {
        searchBtn.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
}

function performSearch() {
    const searchTerm = document.getElementById('search-input').value;
    if (searchTerm.trim()) {
        window.location.href = `produtos.php?search=${encodeURIComponent(searchTerm)}`;
    }
}

function addToCart(productId) {
    fetch('ajax/add-to-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount();
            showNotification('Produto adicionado ao carrinho!', 'success');
        } else {
            showNotification(data.message || 'Erro ao adicionar produto', 'error');
        }
    })
    .catch(error => {
        showNotification('Erro ao adicionar produto', 'error');
    });
}

function updateCartCount() {
    fetch('ajax/get-cart-count.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('cart-count').textContent = data.count;
    });
}

function initCart() {
    updateCartCount();
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 2rem;
        border-radius: 5px;
        color: white;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    if (type === 'success') {
        notification.style.background = '#28a745';
    } else {
        notification.style.background = '#dc3545';
    }
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function initProductFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const productCards = document.querySelectorAll('.product-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            productCards.forEach(card => {
                if (filter === 'all' || card.dataset.category === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

// Validação de formulário de cadastro
function validateRegistration() {
    const form = document.getElementById('registration-form');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        const conselhoClasse = document.getElementById('conselho_classe').value;
        const numeroConselho = document.getElementById('numero_conselho').value;
        
        if (!conselhoClasse || !numeroConselho) {
            e.preventDefault();
            showNotification('É obrigatório informar o conselho de classe e número de registro', 'error');
        }
    });
}