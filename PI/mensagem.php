<?php
// mensagem.php

// Simulação de envio de confirmação por e-mail (para fins de exemplo)
$email_enviado = true; // Aqui você pode adicionar a lógica real de envio de e-mail

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obrigado pela Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .logo-container {
            margin-bottom: 20px;
        }

        .logo {
            max-width: 100%;
            height: auto;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .contact-info {
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Inserção da logo -->
        <div class="logo-container">
            <img src="images/ET.png" alt="Logo" class="logo">
        </div>

        <h1>Obrigado por Realizar a Compra!</h1>
        <p>As confirmações foram enviadas para o e-mail cadastrado. Qualquer dúvida, nos contate.</p>
        <p class="contact-info">Até logo!</p>
    </div>
</body>
</html>
