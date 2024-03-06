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

  <form action="cadastrar.php" method="post">
    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" required>
  
    <label for="cpf">CPF:</label>
    <input type="text" id="cpf" name="cpf" required pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" title="CPF deve estar no formato XXX.XXX.XXX-XX">
  
    <label for="email">E-mail:</label>
    <input type="email" id="email" name="email" required>
  
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

    // Função para validar o CPF
    function validarCPF(cpf) {
      cpf = cpf.replace(/\D/g, '');
      if (cpf.length !== 11 ||
        cpf === '00000000000' ||
        cpf === '11111111111' ||
        cpf === '22222222222' ||
        cpf === '33333333333' ||
        cpf === '44444444444' ||
        cpf === '55555555555' ||
        cpf === '66666666666' ||
        cpf === '77777777777' ||
        cpf === '88888888888' ||
        cpf === '99999999999') {
        return false;
      }
      var soma = 0;
      var resto;
      for (var i = 1; i <= 9; i++) {
        soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
      }
      resto = (soma * 10) % 11;
      if ((resto === 10) || (resto === 11)) {
        resto = 0;
      }
      if (resto !== parseInt(cpf.substring(9, 10))) {
        return false;
      }
      soma = 0;
      for (var i = 1; i <= 10; i++) {
        soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
      }
      resto = (soma * 10) % 11;
      if ((resto === 10) || (resto === 11)) {
        resto = 0;
      }
      if (resto !== parseInt(cpf.substring(10, 11))) {
        return false;
      }
      return true;
    }

    // Verificar se há mensagens de erro ou sucesso e exibir o pop-up
    <?php
    session_start();

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
