<?php
// Verifica se o parâmetro ID foi passado na URL
if (isset($_GET['id'])) {
    // Obtém o ID do produto da URL
    $produto_id = $_GET['id'];

    // Credenciais do banco de dados
    $host = 'localhost';
    $database = 'aplicativo'; // Nome do seu banco de dados
    $username = 'root';
    $password = ''; // Sua senha, se houver

    // Conexão com o banco de dados
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
        die();
    }

    // Consulta SQL para buscar os detalhes do produto com base no ID
    $sql = "SELECT nome, preco, avaliacao, descricao FROM produtos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    // Consulta SQL para buscar as imagens do produto
    $sql_imagens = "SELECT caminho, principal FROM imagens_produto WHERE produto_id = :produto_id";
    $stmt_imagens = $pdo->prepare($sql_imagens);
    $stmt_imagens->execute(['produto_id' => $produto_id]);
    $imagens = $stmt_imagens->fetchAll(PDO::FETCH_ASSOC);

    // Verifica se o produto foi encontrado no banco de dados
    if ($produto) {
        // Atribui os valores do produto às variáveis
        $nome_produto = $produto['nome'];
        $preco = $produto['preco'];
        $avaliacao = $produto['avaliacao'];
        $descricao = $produto['descricao'];
    } else {
        // Se o produto não for encontrado, redireciona para uma página de erro ou exibe uma mensagem
        echo "Produto não encontrado.";
        exit;
    }
} else {
    // Se o parâmetro ID não for passado na URL, redireciona para uma página de erro ou exibe uma mensagem
    echo "ID do produto não fornecido.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Produto</title>
    <link rel="stylesheet" href="visualizarproduto2.css">
</head>
<body>
    <div class="container">
        <div class="produto">
            <div class="imagens-carousel">
                <?php foreach ($imagens as $key => $imagem): ?>
                    <img src="<?php echo $imagem['caminho']; ?>" alt="Imagem do Produto" onclick="exibirImagemPrincipal('<?php echo $imagem['caminho']; ?>')">
                <?php endforeach; ?>
            </div>
            <div class="imagem-principal">
                <!-- Imagem principal do produto -->
                <img src="<?php echo $imagens[0]['caminho']; ?>" alt="Imagem Principal" id="imagemPrincipal">
            </div>
            <div class="detalhes">
                <h1><?php echo $nome_produto; ?></h1>
                <p class="preco">R$ <?php echo number_format($preco, 2, ',', '.'); ?></p>
                <p class="avaliacao">Avaliação: <?php echo $avaliacao; ?> estrelas</p>
                <p class="descricao">
                    <?php echo $descricao; ?>
                </p>
                <form action="adicionar_carrinho.php" method="post">
                    <label for="quantidade">Quantidade:</label>
                    <input type="number" id="quantidade" name="quantidade" min="1" max="10" value="1" required>
                    <input type="hidden" name="produto_id" value="<?php echo $produto_id; ?>">
                    <button type="submit" class="comprar-button">Adicionar ao Carrinho</button>
                </form>
                
                <!-- Botão para ver o carrinho -->
                <a href="carrinho.php" class="ver-carrinho-button">Ver Carrinho</a>
            </div>
        </div>
    </div>
    </div>
    <div class="centered-image">
    <img src="images/ET.png" alt="">
</div>

    <script>
        function exibirImagemPrincipal(caminhoImagem) {
            var imagemPrincipal = document.getElementById('imagemPrincipal');
            imagemPrincipal.src = caminhoImagem;
        }
    </script>
</body>
</html>

