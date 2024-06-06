<?php
session_start();

// Verifica se a sessão do carrinho está vazia ou não foi definida
if (empty($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = array(); // Inicializa o carrinho como uma matriz vazia
}

// Recupera os itens do carrinho da sessão
$carrinho = $_SESSION['carrinho'];

// Conexão com o banco de dados
$host = 'localhost';
$database = 'aplicativo'; // Nome do seu banco de dados
$username = 'root';
$password = ''; // Sua senha, se houver

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    die();
}

// Função para buscar o preço do produto no banco de dados
function buscarPrecoProduto($pdo, $produto_id) {
    $sql = "SELECT preco FROM produtos WHERE id = :produto_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['produto_id' => $produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    return $produto['preco'];
}

// Função para buscar o caminho da imagem principal do produto no banco de dados
function buscarImagemProduto($pdo, $produto_id) {
    $sql = "SELECT caminho FROM imagens_produto WHERE produto_id = :produto_id AND principal = 1 LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['produto_id' => $produto_id]);
    $imagem = $stmt->fetch(PDO::FETCH_ASSOC);
    return $imagem ? $imagem['caminho'] : 'caminho_para_imagem_padrao.jpg'; // Caminho para uma imagem padrão se não encontrar
}

// Função para calcular o valor total de um produto
function calcularTotalProduto($preco, $quantidade) {
    return $preco * $quantidade;
}

// Função para calcular o valor total da compra
function calcularTotalCompra($carrinho, $pdo) {
    $total = 0;
    foreach ($carrinho as $produto_id => $quantidade) {
        $preco = buscarPrecoProduto($pdo, $produto_id);
        $total += calcularTotalProduto($preco, $quantidade);
    }
    return $total;
}

$total_compra = calcularTotalCompra($carrinho, $pdo);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
            gap: 20px;
        }

        .carrinho, .endereco-form, .pagamento-form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .carrinho {
            flex: 1;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            max-width: 350px; /* Ajustar o tamanho do carrinho */
        }

        .produto {
            width: 100%;
            text-align: center;
            background-color: #fafafa;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .produto img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .produto p {
            margin-top: 10px;
            font-size: 14px;
        }

        .endereco-form, .pagamento-form {
            flex: 1;
            display: flex;
            flex-direction: column;
            max-width: 300px; /* Ajustar o tamanho das divs de formulário */
        }

        .endereco-form h2, .pagamento-form h2 {
            margin-bottom: 20px;
            font-size: 20px;
        }

        .endereco-form label, .pagamento-form label {
            display: block;
            margin: 10px 0 5px;
            font-size: 14px;
        }

        .endereco-form input, .pagamento-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .checkout-button {
            margin-top: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        .checkout-button:hover {
            background-color: #45a049;
        }

        .logo-container {
            text-align: center;
            margin-top: 20px;
        }

        .logo {
            max-width: 100%;
            height: auto;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            font-size: 24px;
        }

        .total-compra {
            margin-top: 20px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Checkout</h1>
    <div class="container">
        <!-- Exibição dos produtos do carrinho -->
        <div class="carrinho">
            <?php if (!empty($carrinho)): ?>
                <?php foreach ($carrinho as $produto_id => $quantidade): ?>
                    <div class="produto">
                        <?php
                            $preco = buscarPrecoProduto($pdo, $produto_id);
                            $caminho_imagem = buscarImagemProduto($pdo, $produto_id);
                        ?>
                        <img src="<?php echo htmlspecialchars($caminho_imagem); ?>" alt="Imagem do Produto">
                        <p>Quantidade: <?php echo $quantidade; ?></p>
                        <p>Valor Total: R$ <?php echo number_format(calcularTotalProduto($preco, $quantidade), 2, ',', '.'); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>O carrinho está vazio.</p>
            <?php endif; ?>
        </div>

        <!-- Formulário de endereço -->
        <div class="endereco-form">
            <h2>Endereço de Entrega</h2>
            <form id="checkout-form" action="finalizar_compra.php" method="post">
                <label for="endereco">Endereço</label>
                <input type="text" id="endereco" name="endereco" required>

                <label for="cidade">Cidade</label>
                <input type="text" id="cidade" name="cidade" required>

                <label for="estado">Estado</label>
                <input type="text" id="estado" name="estado" required>

                <label for="cep">CEP</label>
                <input type="text" id="cep" name="cep" required>

                <p>*FRETE GRÁTIS PARA TODO BRASIL*</p>

                <!-- Inserção da logo -->
                <div class="logo-container">
                    <img src="images/ET.png" alt="Logo" class="logo">
                </div>
            </form>
        </div>

        <!-- Formulário de pagamento -->
        <div class="pagamento-form">
            <h2>Dados do Cartão de Crédito</h2>
            <form id="checkout-form" action="mensagem.php" method="post">
                <label for="nome_cartao">Nome no Cartão</label>
                <input type="text" id="nome_cartao" name="nome_cartao" required>

                <label for="numero_cartao">Número do Cartão</label>
                <input type="text" id="numero_cartao" name="numero_cartao" required>

                <label for="validade_cartao">Validade</label>
                <input type="text" id="validade_cartao" name="validade_cartao" required>

                <label for="cvv">CVV</label>
                <input type="text" id="cvv" name="cvv" required>

                <button type="submit" class="checkout-button">Finalizar Compra</button>
                <div class="total-compra">
                    Valor Total da Compra: R$ <?php echo number_format($total_compra, 2, ',', '.'); ?>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
