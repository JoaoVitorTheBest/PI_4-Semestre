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

// Função para atualizar o status no banco de dados
function atualizarStatus($userId, $novoStatus) {
    global $pdo;

    // Prepara e executa a consulta SQL para atualizar o status do usuário
    $sql = "UPDATE usuarios SET status = :status WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['status' => $novoStatus, 'id' => $userId]);

    // Verifica se a atualização foi bem-sucedida
    if ($stmt->rowCount() > 0) {
        return true; // Atualização bem-sucedida
    } else {
        return false; // Falha na atualização
    }
}

// Consulta SQL para buscar os usuários
$sql = "SELECT id, nome, cpf, email, tipo, status FROM usuarios";
$stmt = $pdo->query($sql);

// Verifica se a consulta retornou algum resultado
if ($stmt->rowCount() > 0) {
    // Exibe os dados em uma tabela HTML
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Nome</th>";
    echo "<th>CPF</th>";
    echo "<th>E-mail</th>";
    echo "<th>Tipo</th>";
    echo "<th>Status</th>";
    echo "<th>Ações</th>"; // Adicionado cabeçalho para a coluna de ações
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr data-user-id='" . $row['id'] . "'>";
        echo "<td>" . $row['nome'] . "</td>";
        echo "<td>" . $row['cpf'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . ($row['tipo'] == 'admin' ? 'Administrador' : 'Estoquista') . "</td>";
        echo "<td>";
        echo "<input type='checkbox' class='status-checkbox' " . ($row['status'] == 'ativo' ? 'checked' : '') . ">";
        echo "<span>" . ($row['status'] == 'ativo' ? 'Ativado' : 'Desativado') . "</span>";
        echo "</td>";
        // Adiciona o botão "Alterar" com base no ID do usuário
        echo "<td><button class='alterar-button' data-user-id='" . $row['id'] . "'>Alterar</button></td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
} else {
    echo "Nenhum usuário encontrado.";
}
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.status-checkbox');
        const buttons = document.querySelectorAll('.alterar-button');
        const nomeUsuarioInput = document.getElementById('nomeUsuario');
        const btnProcurar = document.getElementById('btnProcurar');
        const btnAdicionarUsuario = document.querySelector('button[type="button"][id="btnAdicionarUsuario"]');

        btnProcurar.addEventListener('click', function() {
            const nomeUsuario = nomeUsuarioInput.value.trim().toLowerCase();
            const linhasTabela = document.querySelectorAll('tbody tr');

            linhasTabela.forEach(function(linha) {
                const nomeUsuarioTabela = linha.querySelector('td:first-child').textContent.trim().toLowerCase();
                if (nomeUsuarioTabela.includes(nomeUsuario)) {
                    linha.style.display = 'table-row';
                } else {
                    linha.style.display = 'none';
                }
            });
        });

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const statusText = this.nextElementSibling;
                statusText.textContent = this.checked ? 'Ativado' : 'Desativado';
            });
        });

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.dataset.userId;
                window.location.href = 'alterar.html?id=' + userId;
            });
        });

        btnAdicionarUsuario.addEventListener('click', function() {
            window.location.href = 'cadastrarhtml.php';
        });
    });
</script>
