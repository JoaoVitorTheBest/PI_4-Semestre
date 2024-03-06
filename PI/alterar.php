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

// Verifica se o parâmetro ID foi passado na URL
if (isset($_GET['id'])) {
    // Obtém o ID do usuário da URL e converte para um número inteiro para segurança
    $userId = intval($_GET['id']);

    // Consulta SQL para buscar as informações do usuário com base no ID
    $sql = "SELECT nome, cpf, tipo FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário foi encontrado
    if ($usuario) {
        // Define o cabeçalho Content-Type como application/json
        header('Content-Type: application/json');

        // Retorna as informações do usuário em formato JSON
        echo json_encode($usuario);
    } else {
        // Se o usuário não foi encontrado, retorna um erro
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Usuário não encontrado']);
    }
} else {
    // Se o parâmetro ID não foi fornecido na URL, retorna um erro
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'ID do usuário não fornecido']);
}

// Verifica se os dados do formulário foram recebidos via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtém os dados do formulário
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $tipo = $_POST['grupo']; // Assumindo que o campo no formulário se chama "grupo"

    // Atualiza os dados do usuário no banco de dados
    $sql = "UPDATE usuarios SET nome = :nome, cpf = :cpf, tipo = :tipo WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id, 'nome' => $nome, 'cpf' => $cpf, 'tipo' => $tipo]);

    // Verifica se a atualização foi bem-sucedida
    if ($stmt->rowCount() > 0) {
        // Retorna uma resposta de sucesso
        echo json_encode(['success' => true]);
    } else {
        // Retorna uma mensagem de erro
        echo json_encode(['success' => false, 'error' => 'Erro ao atualizar usuário']);
    }
}
?>
