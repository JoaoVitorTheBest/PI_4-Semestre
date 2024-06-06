<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Produto</title>
    <link rel="stylesheet" href="visualizarproduto.css">
</head>
<body>
    <div class="container">
        <div class="produto">
            <div class="imagens-carousel">
                <?php foreach ($imagens as $key => $imagem): ?>
                    <img src="<?php echo $imagem['caminho']; ?>" alt="Imagem do Produto" onclick="exibirImagemPrincipal('<?php echo $imagem['caminho']; ?>')">
                <?php endforeach; ?>
            </div>
            <div class="imagem-principal">
                <!-- Imagem principal do produto -->
                <img src="<?php echo $imagens[0]['caminho']; ?>" alt="Imagem Principal" id="imagemPrincipal">
            </div>
            <div class="detalhes">
                <h1><?php echo $nome_produto; ?></h1>
                <p class="preco">R$ <?php echo number_format($preco, 2, ',', '.'); ?></p>
                <p class="avaliacao">Avaliação: <?php echo $avaliacao; ?> estrelas</p>
                <p class="descricao">
                    <?php echo $descricao; ?>
                </p>
                <button class="comprar-button">Comprar</button>
            </div>
        </div>
    </div>

    <script>
        function exibirImagemPrincipal(caminhoImagem) {
            var imagemPrincipal = document.getElementById('imagemPrincipal');
            imagemPrincipal.src = caminhoImagem;
        }
    </script>
</body>
</html>
