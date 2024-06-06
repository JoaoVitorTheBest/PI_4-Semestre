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
    echo json_encode(['success' => false, 'error' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
    exit;
}

// Consulta SQL para buscar os produtos
$sql = "SELECT id, nome, qtd_estoque, preco, status FROM produtos ORDER BY id DESC";
$stmt = $pdo->query($sql);

// Se não houver produtos, exibe uma mensagem na tabela
if ($stmt->rowCount() === 0) {
    echo "<tr><td colspan='5'>Nenhum produto encontrado.</td></tr>";
    exit;
}

// Exibe os produtos em HTML
// Exibe os produtos em HTML
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  echo "<tr data-product-id='" . $row['id'] . "'>";
  echo "<td>" . $row['id'] . "</td>"; // Adiciona o código do produto
  echo "<td>" . $row['nome'] . "</td>";
  echo "<td>" . $row['qtd_estoque'] . "</td>";
  echo "<td>" . ($row['status'] == 'ativo' ? 'Ativo' : 'Desativado') . "</td>";
  echo "<td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>";
  
  echo "<td>";
  echo "<a href='alterarproduto.html?id=" . $row['id'] . "'>Alterar</a> | ";
  echo "<a href='visualizarproduto.php?id=" . $row['id'] . "' class='visualizar-button'>Visualizar</a>";
  if ($row['status'] == 'ativo') {
      echo " | <button class='inativar-button' data-product-id='" . $row['id'] . "'>Inativar</button>";
      echo " | <button class='ativar-button' data-product-id='" . $row['id'] . "' style='display: none;'>Ativar</button>";
  } else {
      echo " | <button class='inativar-button' data-product-id='" . $row['id'] . "' style='display: none;'>Inativar</button>";
      echo " | <button class='ativar-button' data-product-id='" . $row['id'] . "'>Ativar</button>";
  }
  echo "</td>";
  echo "</tr>";
}

?>