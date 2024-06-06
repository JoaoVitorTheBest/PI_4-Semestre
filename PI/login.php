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

// Verifica se os campos de e-mail e senha foram enviados via POST
if (isset($_POST['email']) && isset($_POST['senha'])) {
    // Obtém os valores enviados
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Consulta SQL para buscar o usuário pelo e-mail
    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário foi encontrado e se a senha corresponde
    if ($usuario) {
        // Descriptografa a senha armazenada no banco de dados
        $senhaArmazenada = $usuario['senha'];

        // Verifica se a senha fornecida pelo usuário corresponde à senha armazenada
        if (password_verify($senha, $senhaArmazenada)) {
            // Login bem-sucedido
            // Define o tipo de usuário na sessão
          $_SESSION['tipo_usuario'] = $usuario['tipo'];
            // Redireciona para a página principal
            header("Location: principalhtml.php");
            exit();
        } else {
            // Senha incorreta
            $_SESSION['mensagem_erro'] = "Usuário ou Senha incorretos";
            header("Location: login.php");
            exit();
        }
    } else {
        // Usuário não encontrado
        $_SESSION['mensagem_erro'] = "Usuário não cadastrado no sistema";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="login.css" media="screen" />
</head>
<body>
  <div class="login-box">
    <h2>Login</h2>
    <?php
    // Exibe mensagem de erro, se existir
    if (isset($_SESSION['mensagem_erro'])) {
        echo "<p class='error-message'>" . $_SESSION['mensagem_erro'] . "</p>";
        unset($_SESSION['mensagem_erro']); // Limpa a mensagem de erro para que não seja exibida novamente
    }
    ?>
    <form action="login.php" method="POST">
      <div class="user-box">
        <input type="email" name="email" required="">
        <label>Email</label>
      </div>
      <div class="user-box">
        <input type="password" name="senha" required="">
        <label>Senha</label>
      </div>
      <button type="submit" class="login-button">Entrar</button>
    </form>
  </div>
</body>
</html>
