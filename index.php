<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$produtos_promocao = getProductsOnSale();
$produtos_vencimento = getProductsNearExpiry();
$produtos_novidades = getNewProducts();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. Derm - Produtos para Estética e Botox</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <!-- Banner Principal -->
        <section class="hero-banner">
            <div class="container">
                <div class="banner-content">
                    <h1>Dr. Derm</h1>
                    <p>Produtos profissionais para estética e botox</p>
                    <a href="produtos.php" class="btn-primary">Ver Produtos</a>
                </div>
            </div>
        </section>

        <!-- Produtos em Promoção -->
        <section class="products-section">
            <div class="container">
                <h2><i class="fas fa-fire"></i> Produtos em Promoção</h2>
                <div class="products-grid">
                    <?php foreach($produtos_promocao as $produto): ?>
                        <div class="product-card">
                            <img src="assets/images/products/<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>">
                            <h3><?php echo $produto['nome']; ?></h3>
                            <p class="brand"><?php echo $produto['marca']; ?></p>
                            <div class="price">
                                <span class="old-price">R$ <?php echo number_format($produto['preco_original'], 2, ',', '.'); ?></span>
                                <span class="current-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></span>
                            </div>
                            <button class="btn-cart" onclick="addToCart(<?php echo $produto['id']; ?>)">
                                <i class="fas fa-cart-plus"></i> Adicionar
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Produtos Próximos do Vencimento -->
        <section class="products-section">
            <div class="container">
                <h2><i class="fas fa-clock"></i> Oportunidades - Próximos do Vencimento</h2>
                <div class="products-grid">
                    <?php foreach($produtos_vencimento as $produto): ?>
                        <div class="product-card expiry-product">
                            <div class="expiry-badge">Vence em <?php echo $produto['dias_vencimento']; ?> dias</div>
                            <img src="assets/images/products/<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>">
                            <h3><?php echo $produto['nome']; ?></h3>
                            <p class="brand"><?php echo $produto['marca']; ?></p>
                            <p class="expiry">Vencimento: <?php echo date('d/m/Y', strtotime($produto['vencimento'])); ?></p>
                            <div class="price">
                                <span class="current-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></span>
                            </div>
                            <button class="btn-cart" onclick="addToCart(<?php echo $produto['id']; ?>)">
                                <i class="fas fa-cart-plus"></i> Adicionar
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Novidades -->
        <section class="products-section">
            <div class="container">
                <h2><i class="fas fa-star"></i> Novidades</h2>
                <div class="products-grid">
                    <?php foreach($produtos_novidades as $produto): ?>
                        <div class="product-card">
                            <div class="new-badge">Novo</div>
                            <img src="assets/images/products/<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>">
                            <h3><?php echo $produto['nome']; ?></h3>
                            <p class="brand"><?php echo $produto['marca']; ?></p>
                            <p class="indication"><?php echo $produto['indicacao']; ?></p>
                            <div class="price">
                                <span class="current-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></span>
                            </div>
                            <button class="btn-cart" onclick="addToCart(<?php echo $produto['id']; ?>)">
                                <i class="fas fa-cart-plus"></i> Adicionar
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <!-- WhatsApp Flutuante -->
    <a href="https://wa.me/5511999999999" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script src="assets/js/main.js"></script>
</body>
</html>