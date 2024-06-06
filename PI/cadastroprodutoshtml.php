<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro de Produtos</title>
  <link rel="stylesheet" href="cadastroprodutos.css">
</head>
<body>
  <h1>Cadastro de Produtos</h1>
  
  <!-- Div para exibir a mensagem de confirmação -->
  <div id="mensagem"></div>

  <form id="formProduto" enctype="multipart/form-data" method="post">
    <label for="nome">Nome do Produto:</label>
    <input type="text" id="nome" name="nome" maxlength="200" required>
    <label for="avaliacao">Avaliação:</label>
    <input type="number" id="avaliacao" name="avaliacao" min="1" max="5" step="0.5" required>
    <label for="descricao">Descrição Detalhada:</label>
    <textarea id="descricao" name="descricao" rows="5" maxlength="2000" required></textarea>
    <label for="preco">Preço:</label>
    <input type="number" id="preco" name="preco" min="0" step="0.01" required>
    <label for="qtdEstoque">Quantidade em Estoque:</label>
    <input type="number" id="qtdEstoque" name="qtdEstoque" min="0" step="1" required>
    
    <!-- Botão "Escolher Ficheiros" para múltiplas imagens -->
    <label for="imagens">Imagens:</label>
    <input type="file" id="imagens" class="imagens" name="imagens[]" accept="image/*" multiple required>
    
    <!-- Opções de seleção da imagem principal -->
    <label for="imagem_principal">Imagem Principal:</label>
    <select id="imagem_principal" name="imagem_principal" required>
      <option value="">Selecionar</option>
    </select>
    
    <input type="submit" value="Salvar">
    <button type="button" id="btnCancelar">Cancelar</button>
  </form>

  <script>
    // Função para exibir a mensagem de confirmação em um pop-up
    function exibirMensagem(mensagem) {
      alert(mensagem);
    }

    // Verifica se há uma mensagem de confirmação na URL e a exibe, se houver
    var params = new URLSearchParams(window.location.search);
    var mensagem = params.get('message');
    if (mensagem) {
      exibirMensagem(mensagem);
    }

    // Evento que atualiza as opções de imagem principal quando novos arquivos são selecionados
    document.getElementById('imagens').addEventListener('change', function() {
      var selectElement = document.getElementById('imagem_principal');
      // Limpa as opções atuais
      selectElement.innerHTML = '';
      // Adiciona as novas opções
      Array.from(this.files).forEach(function(file, index) {
        var option = document.createElement('option');
        option.value = index;
        option.textContent = file ? file.name : 'Selecionar';
        selectElement.appendChild(option);
      });
    });

    // Evento para submeter o formulário via AJAX
    document.getElementById('formProduto').onsubmit = function(event) {
      event.preventDefault(); // Impede o envio padrão do formulário

      var form = this;
      var formData = new FormData(form);

      // Enviar os dados do formulário para cadastroprodutos.php usando AJAX
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'cadastroprodutos.php', true);
      xhr.onload = function() {
        if (xhr.status === 200) {
          // Limpar os campos do formulário
          form.reset();
          // Exibir mensagem de confirmação
          exibirMensagem('Produto cadastrado com sucesso!');
        } else {
          alert('Erro ao processar a requisição.');
        }
      };
      xhr.onerror = function() {
        alert('Erro de conexão.');
      };
      xhr.send(formData);
    };
  </script>
</body>
</html>
