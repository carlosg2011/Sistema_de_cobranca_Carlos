<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registro - Sistema de Cobranças</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            min-height: 100vh;
            justify-content: center;
            align-items: center;
        }

        .card {
            background-color: #ffffff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 420px;
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }

        label {
            font-weight: 600;
            font-size: 0.95rem;
            color: #444;
            display: block;
            margin-bottom: 0.3rem;
        }

        input {
            width: 100%;
            padding: 0.6rem;
            margin-bottom: 1.2rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }

        button {
            width: 100%;
            background-color: #4a90e2;
            color: white;
            font-weight: 600;
            border: none;
            padding: 0.75rem;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #3c7ac7;
        }

        .footer {
            margin-top: 1rem;
            text-align: center;
            font-size: 0.95rem;
        }

        .footer a {
            color: #4a90e2;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .error {
            background-color: #fdecea;
            color: #d93025;
            padding: 0.8rem 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.95rem;
            display: none;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2>Registro</h2>

        <!-- Mensagem de erro -->
        <div class="error" id="errorMessage"></div>

        <form id="registerForm">
            <label for="name">Nome Completo</label>
            <input type="text" id="name" name="name" required autofocus>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>

            <label for="password_confirmation">Confirmar Senha</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>

            <button type="submit">Registrar</button>
        </form>

        <div class="footer">
            Já possui conta? <a href="/login">Fazer login</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function () {
    $("#registerForm").submit(function (e) {
        e.preventDefault();

        const name = $("#name").val().trim();
        const email = $("#email").val().trim();
        const password = $("#password").val();
        const password_confirmation = $("#password_confirmation").val();
        const errorMsg = $("#errorMessage");

        errorMsg.hide().html('');

        if (password !== password_confirmation) {
            errorMsg.text("As senhas não coincidem.").show();
            return;
        }

        $.ajax({
            url: "http://localhost:8000/api/register",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                name,
                email,
                password,
                password_confirmation
            }),
            success: function (response) {
                if (response.access_token) {
                    localStorage.setItem("auth_token", response.access_token);
                    window.location.href = "/charges";
                } else {
                    errorMsg.text(response.message || "Erro ao cadastrar.").show();
                }
            },
            error: function (xhr) {
                const res = xhr.responseJSON;
                let msg = '';

                if (res?.errors) {
                    $.each(res.errors, function (key, errors) {
                        errors.forEach(error => {
                            msg += `<p>${error}</p>`;
                        });
                    });
                } else if (res?.message) {
                    msg = `<p>${res.message}</p>`;
                } else {
                    msg = 'Erro inesperado. Tente novamente.';
                }

                errorMsg.html(msg).show();
            }
        });
    });
});
</script>

</body>
</html>
