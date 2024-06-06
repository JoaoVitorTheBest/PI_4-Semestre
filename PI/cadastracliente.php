<?php
require 'config.php'; // Arquivo que contém as credenciais de conexão com o banco de dados

$nome = $_POST['nome'];
$cpf = $_POST['cpf'];
$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "INSERT INTO usuarios (nome, cpf, email, senha, tipo, status) VALUES (:nome, :cpf, :email, :senha, 'cliente', 'ativo')";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'nome' => $nome,
    'cpf' => $cpf,
    'email' => $email,
    'senha' => md5($senha)
]);

header("Location: logincliente.html");
?>
