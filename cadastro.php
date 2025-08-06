<?php
session_start();
require_once 'config/database.php';

if ($_POST) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $telefone = $_POST['telefone'];
    $conselho_classe = $_POST['conselho_classe'];
    $numero_conselho = $_POST['numero_conselho'];
    $cpf = $_POST['cpf'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, telefone, conselho_classe, numero_conselho, cpf) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $senha, $telefone, $conselho_classe, $numero_conselho, $cpf]);
        
        $_SESSION['success'] = 'Cadastro realizado com sucesso!';
        header('Location: login.php');
        exit;
    } catch(PDOException $e) {
        $error = 'Erro ao cadastrar usuário';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Dr. Derm</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
        }
        .error {
            color: #dc3545;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="form-container">
                <h2>Cadastro de Profissional</h2>
                <p>Cadastre-se para ter acesso aos produtos profissionais</p>
                
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" id="registration-form">
                    <div class="form-group">
                        <label for="nome">Nome Completo *</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">E-mail *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="senha">Senha *</label>
                        <input type="password" id="senha" name="senha" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label for="telefone">Telefone *</label>
                        <input type="tel" id="telefone" name="telefone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cpf">CPF *</label>
                        <input type="text" id="cpf" name="cpf" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="conselho_classe">Conselho de Classe *</label>
                        <select id="conselho_classe" name="conselho_classe" required>
                            <option value="">Selecione...</option>
                            <option value="CRM">CRM - Conselho Regional de Medicina</option>
                            <option value="CRO">CRO - Conselho Regional de Odontologia</option>
                            <option value="CRBM">CRBM - Conselho Regional de Biomedicina</option>
                            <option value="CRF">CRF - Conselho Regional de Farmácia</option>
                            <option value="COREN">COREN - Conselho Regional de Enfermagem</option>
                            <option value="CRFA">CRFA - Conselho Regional de Fisioterapia</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="numero_conselho">Número do Registro *</label>
                        <input type="text" id="numero_conselho" name="numero_conselho" required>
                    </div>
                    
                    <button type="submit" class="btn-submit">Cadastrar</button>
                </form>
                
                <p style="text-align: center; margin-top: 1rem;">
                    Já tem conta? <a href="login.php">Faça login</a>
                </p>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>