<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cobrança PIX</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f8f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        h1 {
            color: #3DA700;
        }

        .info {
            font-size: 15px;
            margin: 10px 0;
        }

        .card {
            background: #3DA700;
            padding: 16px;
            border-radius: 12px;
            text-align: center;
            margin: 20px 0;
        }

        .card h2 {
            color: #fff;
            margin-bottom: 10px;
        }

        .qrcode {
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            display: inline-block;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
-

<body>
    <div class="container">
        <h1>Cobrança PIX</h1>

        <p>Olá {{ $cobranca->cliente_nome ?? 'Cliente' }},</p>

        <p class="info">
            Segue abaixo a cobrança referente a <strong>{{ $cobranca->descricao }}</strong>.
        </p>

        <p class="info"><strong>Valor:</strong> R$ {{ number_format($cobranca->valor, 2, ',', '.') }}</p>
        <p class="info"><strong>Cidade:</strong> {{ $cobranca->cidade }}</p>

        <div class="qrcode">
            <img src="{{ $qrcodeUrl }}" alt="QR Code PIX" style="width:200px;">
        </div>
        <p><strong>Código copia e cola:</strong></p>
        <pre style="font-size:12px; white-space: pre-wrap;">{{ $cobranca->pix_codigo }}</pre>

        <div class="footer">
            <p>Este e-mail foi enviado automaticamente pelo sistema OrganizeMEI.</p>
        </div>
    </div>
</body>

</html>
