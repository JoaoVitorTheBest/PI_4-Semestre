<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lista de Usuários</title>
  <link rel="stylesheet" href="listar.css">
</head>
<body>
  <h1>Lista de Usuários</h1>

  <div class="pesquisa">
  <input type="text" placeholder="Nome do Usuário" id="nomeUsuario">
  <button type="button" id="btnProcurar">Procurar</button>
    <button type="button" id="btnAdicionarUsuario">+ Adicionar Usuário</button>
  </div>

  <table>
    <thead></thead>
    <tbody>
      <?php include 'listarusuarios.php'; ?>
    </tbody>
  </table>

  <script>
    // Seleciona todos os checkboxes com a classe 'status-checkbox'
    const checkboxes = document.querySelectorAll('.status-checkbox');

    // Para cada checkbox, adiciona um ouvinte de eventos para o evento 'change'
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Seleciona o elemento irmão (span) que contém o texto do status
            const statusText = this.nextElementSibling;

            // Verifica se o checkbox está marcado
            if (this.checked) {
                // Se estiver marcado, define o texto como 'Ativado'
                statusText.textContent = 'Ativado';
            } else {
                // Se não estiver marcado, define o texto como 'Desativado'
                statusText.textContent = 'Desativado';
            }
        });
    });
  </script>
</body>
</html>