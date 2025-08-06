<?php
function getProductsOnSale() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM produtos WHERE promocao = 1 LIMIT 8");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductsNearExpiry() {
    global $pdo;
    $stmt = $pdo->query("SELECT *, DATEDIFF(vencimento, NOW()) as dias_vencimento FROM produtos WHERE vencimento <= DATE_ADD(NOW(), INTERVAL 60 DAY) AND vencimento > NOW() LIMIT 8");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getNewProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM produtos WHERE data_cadastro >= DATE_SUB(NOW(), INTERVAL 30 DAY) LIMIT 8");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isProfessional() {
    return isset($_SESSION['conselho_classe']) && !empty($_SESSION['conselho_classe']);
}

function addToCart($produto_id, $quantidade = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$produto_id])) {
        $_SESSION['cart'][$produto_id] += $quantidade;
    } else {
        $_SESSION['cart'][$produto_id] = $quantidade;
    }
}

function getCartTotal() {
    if (!isset($_SESSION['cart'])) return 0;
    
    global $pdo;
    $total = 0;
    
    foreach ($_SESSION['cart'] as $produto_id => $quantidade) {
        $stmt = $pdo->prepare("SELECT preco FROM produtos WHERE id = ?");
        $stmt->execute([$produto_id]);
        $produto = $stmt->fetch();
        
        if ($produto) {
            $total += $produto['preco'] * $quantidade;
        }
    }
    
    return $total;
}
?>