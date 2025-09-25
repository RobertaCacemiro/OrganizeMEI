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

        .btn {
            display: inline-block;
            background: #3DA700;
            color: #fff;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }

        .btn:hover {
            background: #2f7f00;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Cobrança PIX</h1>

        <p>Olá, {{$cobranca->cliente_nome ?? 'Cliente' }}, tudo bem?</p>

        <p class="info">
            Você recebeu uma cobrança de <strong>{{$cobranca->emitente }}</strong>,
            <br>Seguem os detalhes:
        </p>

        <ul>
            <li><strong>Valor:</strong> R$ {{ number_format($cobranca->valor, 2, ',', '.') }}</li>
            <li><strong><strong>Cidade:</strong> {{ $cobranca->cidade }}</li>

        </ul>

        <p class="info"><strong>Valor:</strong> R$ {{ number_format($cobranca->valor, 2, ',', '.') }}</p>
        <p class="info"><strong>Cidade:</strong> {{ $cobranca->cidade }}</p>

        <p>
            Escaneie o QR Code no PDF em anexo para realizar o pagamento.
        </p>

        <p>
            <!-- <a href="http://127.0.0.1:8000/comprovante" class="btn">Enviar Comprovante</a> -->
            <!-- <a href="{{ url('/comprovante/' . $cobranca->key) }}" class="btn">Enviar Comprovante</a> -->
            <a href="{{ url('/comprovante/' . $cobranca->key) }}" class="btn">Enviar Comprovante</a>


        </p>

        <div class="qrcode">
            <img src="{{ $qrcodeCid }}" alt="QR Code PIX" style="width:200px;">
        </div>

        <div class="footer">
            <p>Este e-mail foi enviado automaticamente pelo sistema OrganizeMEI.</p>
        </div>
    </div>
</body>

</html>
