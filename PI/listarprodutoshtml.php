<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listar Produtos</title>
  <link rel="stylesheet" href="listar.css">
</head>
<body>
  <h1>Lista de Produtos</h1>
  
  <div class="pesquisa">
    <input type="text" placeholder="Nome do Produto" id="nomeProduto">
    <button type="button" id="btnProcurar">Procurar</button>
    <button type="button" id="btnAdicionarProduto">+ Adicionar Produto</button>
  </div>
  
  <table>
    <thead>
        <tr>
            <th>Codigo</th>
            <th>Nome</th>
            <th>Quantidade em Estoque</th>
            <th>Status</th>
            <th>Preço</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php include 'listarprodutos.php'; ?>
    </tbody>
  </table>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
    // Adiciona um ouvinte de eventos para o clique no botão "Procurar"
    const btnProcurar = document.getElementById('btnProcurar');
    btnProcurar.addEventListener('click', function() {
        const nomeProduto = document.getElementById('nomeProduto').value.trim().toLowerCase();
        const linhasTabela = document.querySelectorAll('tbody tr');

        linhasTabela.forEach(function(linha) {
            const nomeProdutoTabela = linha.querySelector('td:nth-child(2)').textContent.trim().toLowerCase(); // Alteração aqui para selecionar a segunda coluna (nome)
            if (nomeProdutoTabela.includes(nomeProduto)) {
                linha.style.display = 'table-row';
            } else {
                linha.style.display = 'none';
            }
        });
    });

    // Adiciona um ouvinte de eventos para o clique no botão "Adicionar Produto"
    const btnAdicionarProduto = document.getElementById('btnAdicionarProduto');
    btnAdicionarProduto.addEventListener('click', function() {
        window.location.href = 'cadastroprodutoshtml.php';
    });

    // Restante do seu código para inativar e ativar produtos...
});

        // Adiciona um ouvinte de eventos para o clique no botão "Adicionar Produto"
        const btnAdicionarProduto = document.getElementById('btnAdicionarProduto');
        btnAdicionarProduto.addEventListener('click', function() {
            window.location.href = 'cadastroprodutoshtml.php';
        });

        // Adiciona um ouvinte de eventos para o clique no botão "Inativar"
        document.querySelectorAll('.inativar-button').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                // Atualiza o texto do status para 'Desativado'
                const statusElement = document.querySelector('tr[data-product-id="' + productId + '"] td:nth-child(4)');
                statusElement.textContent = 'Desativado';
                // Oculta o botão "Inativar"
                this.style.display = 'none';
                // Mostra o botão "Ativar"
                const activateButton = document.querySelector('button[data-product-id="' + productId + '"].ativar-button');
                activateButton.style.display = 'inline-block';
            });
        

        // Adiciona um ouvinte de eventos para o clique no botão "Ativar"
        document.querySelectorAll('.ativar-button').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                // Atualiza o texto do status para 'Ativo'
                const statusElement = document.querySelector('tr[data-product-id="' + productId + '"] td:nth-child(4)');
                statusElement.textContent = 'Ativo';
                // Oculta o botão "Ativar"
                this.style.display = 'none';
                // Mostra o botão "Inativar"
                const inactivateButton = document.querySelector('button[data-product-id="' + productId + '"].inativar-button');
                inactivateButton.style.display = 'inline-block';
            });
        });
    });
  </script>
</body>
</html>