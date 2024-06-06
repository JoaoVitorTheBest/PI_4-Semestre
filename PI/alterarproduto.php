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
    echo json_encode(['error' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
    exit();
}

// Verifica se o ID do produto foi enviado via GET ou POST
$produtoId = isset($_POST['produtoId']) ? $_POST['produtoId'] : (isset($_GET['id']) ? $_GET['id'] : null);
if (!$produtoId) {
    // Se nenhum ID for encontrado, retorna um erro
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'ID do produto não especificado']);
    exit();
}

// Verifica o método da requisição
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Realiza o update dos dados do produto no banco de dados
    try {
        $sql = "UPDATE produtos SET nome = :nome, avaliacao = :avaliacao, descricao = :descricao, preco = :preco, qtd_estoque = :qtd_estoque WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nome' => $_POST['nome'],
            'avaliacao' => $_POST['avaliacao'],
            'descricao' => $_POST['descricao'],
            'preco' => $_POST['preco'],
            'qtd_estoque' => $_POST['qtdEstoque'],
            'id' => $produtoId
        ]);
        
        // Verifica se há imagens a serem removidas
        if (!empty($_POST['imagensRemover'])) {
            $imagensRemover = json_decode($_POST['imagensRemover']);
            // Exclui as imagens do banco de dados
            $stmt = $pdo->prepare("DELETE FROM imagens_produto WHERE id = ?");
            foreach ($imagensRemover as $imagemId) {
                $stmt->execute([$imagemId]);
            }
        }

        // Obtém o índice da imagem principal
        $imagemPrincipalIndex = isset($_POST['imagemPrincipalIndex']) ? $_POST['imagemPrincipalIndex'] : null;
        $imagemPrincipalId = null;

        // Se um índice válido foi recebido, obtenha o ID correspondente da imagem principal
        if ($imagemPrincipalIndex !== null && isset($_POST['imagem_principal'])) {
            $imagemPrincipalId = $_POST['imagem_principal'][$imagemPrincipalIndex];
        }

        // Atualiza a marcação de imagem principal no banco de dados
        $sqlUpdatePrincipal = "UPDATE imagens_produto SET principal = 0 WHERE produto_id = ?";
        $stmtUpdatePrincipal = $pdo->prepare($sqlUpdatePrincipal);
        $stmtUpdatePrincipal->execute([$produtoId]);

        if ($imagemPrincipalId !== null) {
            $sqlUpdatePrincipal = "UPDATE imagens_produto SET principal = 1 WHERE id = ?";
            $stmtUpdatePrincipal = $pdo->prepare($sqlUpdatePrincipal);
            $stmtUpdatePrincipal->execute([$imagemPrincipalId]);
        }

        // Agora, insira as novas fotos
        foreach ($_FILES['imagens']['tmp_name'] as $key => $tmp_name) {
            $file_name = uniqid() . '_' . basename($_FILES['imagens']['name'][$key]);
            $file_tmp = $_FILES['imagens']['tmp_name'][$key];
            $file_type = $_FILES['imagens']['type'][$key];
            $file_error = $_FILES['imagens']['error'][$key];
            if ($file_error === 0) {
                $destination = $_SERVER['DOCUMENT_ROOT'] . '/PI/images/' . $file_name;
                move_uploaded_file($file_tmp, $destination);
                // Verifica se esta é a imagem principal
                $principal = ($imagemPrincipalId && $imagemPrincipalId == 'nova_imagem_' . $key) ? 1 : 0;
                // Inserir caminho da imagem no banco de dados
                $stmt = $pdo->prepare("INSERT INTO imagens_produto (produto_id, caminho, principal) VALUES (?, ?, ?)");
                $stmt->execute([$produtoId, 'images/' . $file_name, $principal]);
            } else {
                echo json_encode(['error' => 'Erro ao fazer upload da imagem: ' . $_FILES['imagens']['name'][$key]]);
                exit();
            }
        }

        // Retorna mensagem de sucesso
        echo 'Produto atualizado com sucesso.';
    } catch (PDOException $e) {
        // Se houver algum erro durante a atualização, retorna uma mensagem de erro
        echo json_encode(['error' => 'Erro ao atualizar os detalhes do produto: ' . $e->getMessage()]);
    }
} else {
    // Método GET: Busca os detalhes do produto no banco de dados
    try {
        $sql = "SELECT id, nome, avaliacao, descricao, preco, qtd_estoque FROM produtos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $produtoId]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        // Busca as fotos do produto
        $sql = "SELECT id, caminho, principal FROM imagens_produto WHERE produto_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$produtoId]);
        $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Adiciona as fotos ao array do produto
        $produto['fotos'] = $fotos;

        // Verifica se o produto foi encontrado
        if ($produto) {
            // Retorna os detalhes do produto em formato JSON
            echo json_encode($produto);
        } else {
            // Se o produto não for encontrado, retorna uma mensagem de erro
            echo json_encode(['error' => 'Produto não encontrado']);
        }
    } catch (PDOException $e) {
        // Se houver algum erro durante a busca, retorna uma mensagem de erro
        echo json_encode(['error' => 'Erro ao buscar os detalhes do produto: ' . $e->getMessage()]);
    }
}
?>
