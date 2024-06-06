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

// Função para validar o CPF
function validarCPF($cpf) {
    // Remove todos os caracteres não numéricos
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
    } elseif (!validarCPF($cpf)) {
        $_SESSION['mensagem_erro'] = "CPF inválido.";
    } else {
        // Verifica se o CPF já está cadastrado
        global $pdo;
        $sql = "SELECT * FROM usuarios WHERE cpf = :cpf";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $_SESSION['mensagem_erro'] = "Este CPF já está cadastrado.";
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
                // Limpar campos do formulário em caso de sucesso
                $nome = $cpf = $email = $senha = $confirmarSenha = $grupo = '';
            } catch (PDOException $e) {
                $_SESSION['mensagem_erro'] = "Erro ao inserir dados no banco de dados: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro de Usuário</title>
  <link rel="stylesheet" href="cadastrar.css">
  <style>
    /* Estilo para o pop-up */
    .popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: white;
      padding: 20px;
      border: 1px solid #ccc;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      z-index: 9999;
    }
  </style>
</head>
<body>
  <h1>Cadastro de Usuário</h1>

  <!-- Pop-up de mensagem -->
  <div id="popup" class="popup"></div>

  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" value="<?php echo isset($nome) ? $nome : ''; ?>" required>
  
    <label for="cpf">CPF:</label>
    <input type="text" id="cpf" name="cpf" value="<?php echo isset($cpf) ? $cpf : ''; ?>" required pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" title="CPF deve estar no formato XXX.XXX.XXX-XX">
  
    <label for="email">E-mail:</label>
    <input type="email" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
  
    <label for="senha">Senha:</label>
    <input type="password" id="senha" name="senha" required>
  
    <label for="confirmar_senha">Confirmar Senha:</label>
    <input type="password" id="confirmar_senha" name="confirmar_senha" required>
  
    <label for="grupo">Grupo:</label>
    <select id="grupo" name="grupo" required>
      <option value="">Selecione...</option>
      <option value="admin">Administrador</option>
      <option value="estoquista">Estoquista</option>
    </select>
  
    <button type="submit">Cadastrar</button>
  </form>

  <script>
    // Função para exibir o pop-up de mensagem
    function mostrarMensagem(tipo, mensagem) {
      var popup = document.getElementById('popup');
      popup.textContent = mensagem;
      popup.style.display = 'block';
      if (tipo === 'erro') {
        popup.style.backgroundColor = 'pink';
      } else {
        popup.style.backgroundColor = 'lightgreen';
        // Limpar campos do formulário em caso de sucesso
        if (mensagem.includes('sucesso')) {
          document.getElementById('nome').value = '';
          document.getElementById('cpf').value = '';
          document.getElementById('email').value = '';
          document.getElementById('senha').value = '';
          document.getElementById('confirmar_senha').value = '';
          document.getElementById('grupo').selectedIndex = 0;
        }
      }
      // Esconder o pop-up após 3 segundos
      setTimeout(function() {
        popup.style.display = 'none';
      }, 3000);
    }

    // Função para formatar o CPF no padrão brasileiro (XXX.XXX.XXX-XX)
    document.getElementById('cpf').addEventListener('input', function(e) {
      var cpf = e.target.value.replace(/\D/g, ''); // Remove caracteres não numéricos
      if (cpf.length > 3) {
        cpf = cpf.replace(/(\d{3})(\d{1,3})/, '$1.$2');
      }
      if (cpf.length > 7) {
        cpf = cpf.replace(/(\d{3})(\d{1,3})/, '$1.$2');
      }
      if (cpf.length > 11) {
        cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
      }
      e.target.value = cpf;
      
      // Chamada da função validarCPF
      if (cpf.length === 14) {
        if (!validarCPF(cpf)) {
          mostrarMensagem('erro', 'CPF inválido.');
        }
      }
    });

    // Verificar se há mensagens de erro ou sucesso e exibir o pop-up
    <?php
    if (isset($_SESSION['mensagem_erro'])) {
      echo "mostrarMensagem('erro', '" . $_SESSION['mensagem_erro'] . "');";
      unset($_SESSION['mensagem_erro']); // Limpa a mensagem de erro para não exibi-la novamente
    }

    if (isset($_SESSION['mensagem_sucesso'])) {
      echo "mostrarMensagem('sucesso', '" . $_SESSION['mensagem_sucesso'] . "');";
      unset($_SESSION['mensagem_sucesso']); // Limpa a mensagem de sucesso para não exibi-la novamente
    }
    ?>
  </script>
</body>
</html>
