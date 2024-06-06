<?php
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

// Consulta SQL para buscar os produtos e suas imagens principais
$sql = "SELECT p.id, p.nome, p.preco, i.caminho AS imagem_principal
        FROM produtos p
        LEFT JOIN imagens_produto i ON p.id = i.produto_id AND i.principal = 1
        WHERE p.status = 'ativo'";
$stmt = $pdo->query($sql);

// Se não houver produtos, exibe uma mensagem
if ($stmt->rowCount() === 0) {
    echo "<p>Nenhum produto encontrado.</p>";
    exit;
}

// Exibe os produtos em forma de cartões
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<div class='produto-card'>";
    echo "<img src='" . $row['imagem_principal'] . "' alt='Imagem do Produto'>";
    echo "<h2>" . $row['nome'] . "</h2>";
    echo "<p>R$ " . number_format($row['preco'], 2, ',', '.') . "</p>";
    echo "<a href='visualizarproduto2.php?id=" . $row['id'] . "' class='detalhe-button'>Detalhes</a>";
    echo "</div>";
}
?>
