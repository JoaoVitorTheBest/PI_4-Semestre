<?php
session_start(); // Inicia a sessão para acessar as variáveis de sessão

// Verifica se o tipo de usuário é admin
if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') {
    
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela Principal</title>
    <link rel="stylesheet" href="principal.css">
</head>
<body>
    <h1 class="nome">Seja bem-vindo a Loja de Todos</h1>
    <div id="main">
        <div></div>
        <?php
        // Verifica o tipo de usuário e define a variável isAdmin em JavaScript
        if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') {
            echo '<div class="button" onclick="listarUsuarios()">Listar Usuários</div>';
        }
        ?>
        <div class="button" onclick="listarProdutos()">Listar Produtos</div>
        <div class="button">Listar Pedidos</div>
    </div>

    <script>
        // Função para listar usuários, verifica se o usuário é admin antes de redirecionar
        function listarUsuarios() {
            window.location.href = 'listausuarioshtml.php';
        }

        // Função para listar produtos, sempre redireciona
        function listarProdutos() {
            window.location.href = 'listarprodutoshtml.php';
        }
    </script>
</body>
</html>
