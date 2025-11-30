<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cobrança PIX</title>
</head>

<body style="margin:0; padding:0; font-family: Arial, sans-serif; background:#f3f4f6;">
    <div
        style="max-width:600px; margin:20px auto; background:#ffffff; border-radius:10px; padding:24px; box-shadow:0 3px 8px rgba(0,0,0,0.12);">

        <!-- Logo -->
        <div style="text-align:center; margin-bottom:20px;">
            <img src="https://organize-mei-storage.s3.us-east-2.amazonaws.com/logos/logo.png" alt="Logo OrganizeMEI"
                style="max-width:180px; height:auto;">
        </div>

        <!-- Título -->
        <h1 style="color:#3DA700; text-align:center; font-size:22px; margin-bottom:10px;">
            COBRANÇA Nº {{ $cobranca->id }}
        </h1>
        <p style="text-align:center; font-size:14px; color:#555; margin-top:0;">
            Confira os detalhes da cobrança abaixo
        </p>

        <!-- Saudação -->
        <p style="font-size:15px; line-height:1.5; color:#333;">
            Olá <strong>{{ $cobranca->cliente_nome ?? 'caro cliente' }}</strong>,
        </p>

        <!-- Informações do emissor -->
        <p style="font-size:15px; line-height:1.6; margin:10px 0; color:#444;">
            Você recebeu uma cobrança emitida por
            <strong>{{ $cobranca->identification ?? 'N/A' }} - {{ $cobranca->cpf_cnpj }}</strong>.
            Clique no botão abaixo para acessar todos os detalhes e instruções de pagamento.
        </p>

        <!-- Botão -->
        <p style="text-align:center; margin:20px 0;">
            <a href="{{ url('/comprovante/' . $cobranca->key) }}"
                style="display:inline-block; background:#3DA700; color:#ffffff; padding:12px 20px; border-radius:6px; text-decoration:none; font-weight:bold; font-size:15px;">
                Visualizar Cobrança
            </a>
        </p>

        <!-- Alerta -->
        <div
            style="margin-top:25px; padding:14px; background:#fff8e6; border:1px solid #f5c16c; border-radius:6px; color:#7a5a2d; font-size:14px; line-height:1.5;">
            <strong style="color:#d9534f;">⚠️ Atenção:</strong> Antes de realizar o pagamento, confirme que você
            reconhece esta cobrança e que ela corresponde a um serviço ou produto contratado com
            <strong>{{ $cobranca->identification ?? 'N/A' }} - {{ $cobranca->cpf_cnpj ?? '' }}</strong>.<br><br>
            Em caso de dúvidas, entre em contato diretamente com o emissor antes de prosseguir.
        </div>

        <!-- Footer -->
        <div style="margin-top:25px; font-size:12px; color:#777; text-align:center; line-height:1.4;">
            Este e-mail foi enviado automaticamente pelo sistema <strong>OrganizeMEI</strong>.<br>
            Por favor, não responda a esta mensagem.
        </div>
    </div>

</body>

</html>
