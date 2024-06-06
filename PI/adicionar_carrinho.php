<?php
session_start();

// Verifica se o produto_id e a quantidade foram enviados via POST
if(isset($_POST['produto_id']) && isset($_POST['quantidade'])) {
    // Recupera o ID do produto e a quantidade
    $produto_id = $_POST['produto_id'];
    $quantidade = $_POST['quantidade'];

    // Inicia ou retoma a sessão do carrinho
    if(!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = array();
    }

    // Adiciona o produto ao carrinho
    if(isset($_SESSION['carrinho'][$produto_id])) {
        // Se o produto já estiver no carrinho, apenas atualiza a quantidade
        $_SESSION['carrinho'][$produto_id] += $quantidade;
    } else {
        // Se o produto ainda não estiver no carrinho, adiciona com a quantidade especificada
        $_SESSION['carrinho'][$produto_id] = $quantidade;
    }

    // Redireciona de volta para a página do produto com uma mensagem de sucesso
    header("Location: visualizarproduto2.php?id=$produto_id&added=true");
} else {
    // Se o produto_id ou a quantidade não forem enviados, redireciona de volta para a página do produto com uma mensagem de erro
    header("Location: visualizarproduto2.php?id=$produto_id&error=true");
}
?>
