<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$search = $_GET['search'] ?? '';
$categoria = $_GET['categoria'] ?? '';

$sql = "SELECT p.*, c.nome as categoria_nome FROM produtos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.ativo = 1";

if ($search) {
    $sql .= " AND (p.nome LIKE :search OR p.marca LIKE :search OR p.descricao LIKE :search)";
}

if ($categoria) {
    $sql .= " AND p.categoria_id = :categoria";
}

$sql .= " ORDER BY p.nome";

$stmt = $pdo->prepare($sql);

if ($search) {
    $stmt->bindValue(':search', "%$search%");
}
if ($categoria) {
    $stmt->bindValue(':categoria', $categoria);
}

$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar categorias
$categorias = $pdo->query("SELECT * FROM categorias WHERE ativo = 1")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Dr. Derm</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .filters {
            background: white;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .filter-group {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            background: white;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .filter-btn.active, .filter-btn:hover {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <div class="container">
            <h1>Produtos</h1>
            
            <div class="filters">
                <div class="filter-group">
                    <span>Filtrar por categoria:</span>
                    <button class="filter-btn <?php echo !$categoria ? 'active' : ''; ?>" 
                            onclick="window.location.href='produtos.php'">Todos</button>
                    <?php foreach($categorias as $cat): ?>
                        <button class="filter-btn <?php echo $categoria == $cat['id'] ? 'active' : ''; ?>" 
                                onclick="window.location.href='produtos.php?categoria=<?php echo $cat['id']; ?>'">
                            <?php echo $cat['nome']; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <?php if ($search): ?>
                <p>Resultados para: "<strong><?php echo htmlspecialchars($search); ?></strong>" (<?php echo count($produtos); ?> produtos)</p>
            <?php endif; ?>
            
            <div class="products-grid">
                <?php foreach($produtos as $produto): ?>
                    <div class="product-card" data-category="<?php echo $produto['categoria_id']; ?>">
                        <?php if ($produto['promocao']): ?>
                            <div class="new-badge">Promoção</div>
                        <?php endif; ?>
                        
                        <img src="assets/images/products/<?php echo $produto['imagem'] ?: 'default.jpg'; ?>" 
                             alt="<?php echo $produto['nome']; ?>">
                        
                        <h3><?php echo $produto['nome']; ?></h3>
                        <p class="brand"><?php echo $produto['marca']; ?> - <?php echo $produto['modelo']; ?></p>
                        
                        <?php if ($produto['indicacao']): ?>
                            <p class="indication"><?php echo $produto['indicacao']; ?></p>
                        <?php endif; ?>
                        
                        <?php if ($produto['vencimento']): ?>
                            <p class="expiry">Vencimento: <?php echo date('d/m/Y', strtotime($produto['vencimento'])); ?></p>
                        <?php endif; ?>
                        
                        <div class="price">
                            <?php if ($produto['preco_original']): ?>
                                <span class="old-price">R$ <?php echo number_format($produto['preco_original'], 2, ',', '.'); ?></span>
                            <?php endif; ?>
                            <span class="current-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></span>
                        </div>
                        
                        <p class="stock">Estoque: <?php echo $produto['estoque']; ?> unidades</p>
                        
                        <button class="btn-cart" onclick="addToCart(<?php echo $produto['id']; ?>)" 
                                <?php echo $produto['estoque'] <= 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-cart-plus"></i> 
                            <?php echo $produto['estoque'] <= 0 ? 'Sem Estoque' : 'Adicionar'; ?>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($produtos)): ?>
                <div style="text-align: center; padding: 3rem;">
                    <i class="fas fa-search" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                    <h3>Nenhum produto encontrado</h3>
                    <p>Tente ajustar os filtros ou fazer uma nova busca.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <a href="https://wa.me/5511999999999" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>
    
    <script src="assets/js/main.js"></script>
</body>
</html>