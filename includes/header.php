<header class="main-header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <img src="logo dr derm.png" alt="Dr. Derm" class="logo-img">
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="produtos.php">Produtos</a></li>
                    <li><a href="categorias.php">Categorias</a></li>
                    <li><a href="sobre.php">Sobre</a></li>
                </ul>
            </nav>
            
            <div class="header-actions">
                <div class="search-box">
                    <input type="text" placeholder="Buscar produtos..." id="search-input">
                    <button type="button" id="search-btn"><i class="fas fa-search"></i></button>
                </div>
                
                <div class="user-menu">
                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php" class="user-link">
                            <i class="fas fa-user"></i>
                            <?php echo $_SESSION['nome']; ?>
                        </a>
                        <a href="logout.php" class="logout-link">Sair</a>
                    <?php else: ?>
                        <a href="login.php" class="login-link">
                            <i class="fas fa-sign-in-alt"></i> Entrar
                        </a>
                        <a href="cadastro.php" class="register-link">Cadastrar</a>
                    <?php endif; ?>
                </div>
                
                <div class="cart-icon">
                    <a href="carrinho.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count" id="cart-count">
                            <?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>