<?php
session_start();

// Credenciais do banco de dados
$host = 'localhost';
$database = 'aplicativo'; // Altere para o nome do seu banco de dados
$username = 'root';
$password = ''; // Coloque sua senha aqui, se houver

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    die();
}

// Verificação de envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar dados do formulário e remover espaços extras
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmarSenha = $_POST['confirmar_senha'];
    $grupo = $_POST['grupo'];

    // Validação de campos
    if (empty($nome) || empty($cpf) || empty($email) || empty($senha) || empty($confirmarSenha) || empty($grupo)) {
        $_SESSION['mensagem_erro'] = "Todos os campos são obrigatórios.";
    } elseif ($senha !== $confirmarSenha) {
        $_SESSION['mensagem_erro'] = "As senhas não coincidem.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['mensagem_erro'] = "O email informado é inválido.";
    } else {
        // Hash da senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Inserir dados no banco de dados
        $sql = "INSERT INTO usuarios (nome, cpf, email, senha, tipo) VALUES (:nome, :cpf, :email, :senha, :tipo)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':tipo', $grupo);

        try {
            $stmt->execute();
            $_SESSION['mensagem_sucesso'] = "Usuário cadastrado com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['mensagem_erro'] = "Erro ao inserir dados no banco de dados: " . $e->getMessage();
        }
    }

    // Redirecionamento de volta para a página de cadastro
    header("Location: cadastrarhtml.php");
    exit();
}
?>
