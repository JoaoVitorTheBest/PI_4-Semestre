<?php
session_start();

$message = ''; // Inicializa a variável de mensagem

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Processar os dados do formulário

    // Conexão com o banco de dados
    $host = 'localhost';
    $database = 'aplicativo'; // Altere para o nome do seu banco de dados
    $username = 'root';
    $password = ''; // Coloque sua senha aqui, se houver

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        $message = 'Erro ao conectar ao banco de dados: ' . $e->getMessage();
    }

    if ($pdo) {
        $nome = $_POST['nome'];
        $avaliacao = $_POST['avaliacao'];
        $descricao = $_POST['descricao'];
        $preco = $_POST['preco'];
        $qtdEstoque = $_POST['qtdEstoque'];

        // Inserir produto no banco de dados
        try {
            $stmt = $pdo->prepare("INSERT INTO produtos (nome, avaliacao, descricao, preco, qtd_estoque, status) VALUES (?, ?, ?, ?, ?, 'ativo')");
            $stmt->execute([$nome, $avaliacao, $descricao, $preco, $qtdEstoque]);
            $produtoId = $pdo->lastInsertId();

            if ($produtoId) {
                // Processar imagens
                if (!empty($_FILES['imagens']['name'][0])) {
                    foreach ($_FILES['imagens']['tmp_name'] as $key => $tmp_name) {
                        $file_name = uniqid() . '_' . basename($_FILES['imagens']['name'][$key]);
                        $file_tmp = $_FILES['imagens']['tmp_name'][$key];
                        $file_type = $_FILES['imagens']['type'][$key];
                        $file_error = $_FILES['imagens']['error'][$key];
                        if ($file_error === 0) {
                            $destination = $_SERVER['DOCUMENT_ROOT'] . '/PI/images/' . $file_name;
                            move_uploaded_file($file_tmp, $destination);
                            // Inserir caminho da imagem no banco de dados
                            $principal = ($_POST['imagem_principal'] == $key) ? 1 : 0;
                            $stmt = $pdo->prepare("INSERT INTO imagens_produto (produto_id, caminho, principal) VALUES (?, ?, ?)");
                            $stmt->execute([$produtoId, 'images/' . $file_name, $principal]);
                        } else {
                            $message .= 'Erro ao fazer upload da imagem: ' . $_FILES['imagens']['name'][$key];
                        }
                    }
                }
                // Mensagem de sucesso
                $message = 'Produto cadastrado com sucesso!';
            } else {
                $message .= 'Erro ao obter o ID do produto.';
            }
        } catch (PDOException $e) {
            // Mensagem de erro
            $message .= 'Erro ao cadastrar produto: ' . $e->getMessage();
        }
    }

    // Limpa os campos após o cadastro
    $_POST = array();

    // Exibe a mensagem de confirmação
    echo $message;
    // Redireciona para a página de cadastro de produtos com a mensagem como parâmetro na URL
    echo "<script>window.location.href = 'cadastroprodutoshtml.php?message=" . urlencode($message) . "';</script>";
    exit();
}
?>
