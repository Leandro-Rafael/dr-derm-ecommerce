<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $cart_items = [];
    $total = 0;
} else {
    $cart_items = [];
    $total = 0;
    
    foreach ($_SESSION['cart'] as $produto_id => $quantidade) {
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$produto_id]);
        $produto = $stmt->fetch();
        
        if ($produto) {
            $produto['quantidade'] = $quantidade;
            $produto['subtotal'] = $produto['preco'] * $quantidade;
            $cart_items[] = $produto;
            $total += $produto['subtotal'];
        }
    }
}

// Processar remoção de item
if (isset($_GET['remove'])) {
    $produto_id = $_GET['remove'];
    unset($_SESSION['cart'][$produto_id]);
    header('Location: carrinho.php');
    exit;
}

// Processar atualização de quantidade
if ($_POST && isset($_POST['update_cart'])) {
    foreach ($_POST['quantidade'] as $produto_id => $quantidade) {
        if ($quantidade <= 0) {
            unset($_SESSION['cart'][$produto_id]);
        } else {
            $_SESSION['cart'][$produto_id] = $quantidade;
        }
    }
    header('Location: carrinho.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - Dr. Derm</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .cart-container {
            max-width: 800px;
            margin: 2rem auto;
        }
        .cart-item {
            background: white;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        .item-info {
            flex: 1;
        }
        .item-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .quantity-input {
            width: 60px;
            padding: 0.5rem;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .cart-summary {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }
        .empty-cart {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="cart-container">
                <h1>Carrinho de Compras</h1>
                
                <?php if (empty($cart_items)): ?>
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                        <h3>Seu carrinho está vazio</h3>
                        <p>Adicione produtos para continuar com a compra.</p>
                        <a href="produtos.php" class="btn-primary">Ver Produtos</a>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item">
                                <img src="assets/images/products/<?php echo $item['imagem'] ?: 'default.jpg'; ?>" 
                                     alt="<?php echo $item['nome']; ?>">
                                
                                <div class="item-info">
                                    <h3><?php echo $item['nome']; ?></h3>
                                    <p><?php echo $item['marca']; ?> - <?php echo $item['modelo']; ?></p>
                                    <p class="price">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></p>
                                </div>
                                
                                <div class="item-controls">
                                    <input type="number" name="quantidade[<?php echo $item['id']; ?>]" 
                                           value="<?php echo $item['quantidade']; ?>" 
                                           min="1" max="<?php echo $item['estoque']; ?>" 
                                           class="quantity-input">
                                    
                                    <span class="subtotal">
                                        R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?>
                                    </span>
                                    
                                    <a href="carrinho.php?remove=<?php echo $item['id']; ?>" 
                                       class="btn-remove" 
                                       onclick="return confirm('Remover item do carrinho?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div style="text-align: center; margin: 1rem 0;">
                            <button type="submit" name="update_cart" class="btn-primary">
                                Atualizar Carrinho
                            </button>
                        </div>
                    </form>
                    
                    <div class="cart-summary">
                        <h3>Resumo do Pedido</h3>
                        <div style="display: flex; justify-content: space-between; margin: 1rem 0;">
                            <span>Subtotal:</span>
                            <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin: 1rem 0;">
                            <span>Frete:</span>
                            <span style="color: #28a745;">GRÁTIS</span>
                        </div>
                        <hr>
                        <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold;">
                            <span>Total:</span>
                            <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                        </div>
                        
                        <?php if (isLoggedIn() && isProfessional()): ?>
                            <a href="checkout.php" class="btn-primary" style="width: 100%; text-align: center; margin-top: 1rem; display: block;">
                                Finalizar Compra
                            </a>
                        <?php else: ?>
                            <p style="text-align: center; margin-top: 1rem; color: #dc3545;">
                                É necessário fazer login como profissional para finalizar a compra.
                            </p>
                            <a href="login.php" class="btn-primary" style="width: 100%; text-align: center; margin-top: 1rem; display: block;">
                                Fazer Login
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>