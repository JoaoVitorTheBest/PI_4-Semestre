<?php
session_start();
require 'config.php'; // Arquivo que contém as credenciais de conexão com o banco de dados

$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM usuarios WHERE email = :email AND senha = :senha AND status = 'ativo' AND tipo = 'cliente'";
$stmt = $pdo->prepare($sql);
$stmt->execute(['email' => $email, 'senha' => md5($senha)]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['usuario_nome'] = $user['nome'];
    header("Location: comprashtml.php");
} else {
    $_SESSION['login_erro'] = "Email ou senha incorretos.";
    header("Location: logincliente.html");
}
?>
