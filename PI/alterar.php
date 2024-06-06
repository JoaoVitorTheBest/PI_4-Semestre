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

// Verifica se o ID do usuário está disponível nos parâmetros da URL ou no formulário
$userId = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : null);
if (!$userId) {
    // Se nenhum ID for encontrado, retorna um erro
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'ID do usuário não fornecido']);
    exit();
}

// Verifica se os dados do formulário foram recebidos via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtém os dados do formulário
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $tipo = $_POST['grupo']; // Assumindo que o campo no formulário se chama "grupo"

    // Validação do CPF
    function validarCPF($cpf) {
        // Remove todos os caracteres que não sejam dígitos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Verifica se o CPF tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais, o que torna o CPF inválido
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcula o primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += (int) $cpf[$i] * (10 - $i);
        }
        $resto = 11 - ($soma % 11);
        $dv1 = ($resto >= 10) ? 0 : $resto;

        // Calcula o segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += (int) $cpf[$i] * (11 - $i);
        }
        $resto = 11 - ($soma % 11);
        $dv2 = ($resto >= 10) ? 0 : $resto;

        // Verifica se os dígitos verificadores calculados são iguais aos informados no CPF
        if ($dv1 == $cpf[9] && $dv2 == $cpf[10]) {
            return true;
        }

        return false;
    }

    // Validações dos campos
    $errors = [];

    // Validação do CPF
    if (!validarCPF($cpf)) {
        $errors[] = "CPF inválido.";
    }

    // Verifica se as senhas coincidem
    if ($senha !== $confirmar_senha) {
        $errors[] = "As senhas não coincidem.";
    }

    // Verifica se há erros
    if (!empty($errors)) {
        // Retorna uma resposta com os erros encontrados
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }

    // Hash da senha
    $hashed_senha = password_hash($senha, PASSWORD_DEFAULT);

    // Atualiza os dados do usuário no banco de dados
    $sql = "UPDATE usuarios SET nome = :nome, cpf = :cpf, senha = :senha, tipo = :tipo WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $userId, 'nome' => $nome, 'cpf' => $cpf, 'senha' => $hashed_senha, 'tipo' => $tipo]);

    // Verifica se a atualização foi bem-sucedida
    if ($stmt->rowCount() > 0) {
        // Retorna uma resposta de sucesso
        echo json_encode(['success' => true]);
    } else {
        // Retorna uma mensagem de erro
        echo json_encode(['success' => false, 'error' => 'Erro ao atualizar usuário']);
    }
    exit();
} else {
    // Recupera os dados do usuário
    $sql = "SELECT nome, cpf, tipo FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        // Se o usuário não for encontrado, retorna um erro
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Usuário não encontrado']);
        exit();
    }
    // Define o cabeçalho Content-Type como application/json
    header('Content-Type: application/json');

    // Retorna as informações do usuário em formato JSON
    echo json_encode($usuario);
    exit();
}
?>
