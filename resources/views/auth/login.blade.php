<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Cobranças</title>
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
            max-width: 400px;
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
        <h2>Login</h2>

        <div class="error" id="errorMessage"></div>

        <form id="loginForm">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Entrar</button>
        </form>

        <div class="footer">
            Não tem uma conta? <a href="/register">Cadastre-se</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>


    $("#loginForm").submit(function(e) {
        e.preventDefault();

        const email = $("#email").val().trim();
        const password = $("#password").val().trim();
        const errorMsg = $("#errorMessage");

        errorMsg.hide().text("");

        if (!email || !validateEmail(email)) {
            errorMsg.text("Informe um e-mail válido.").show();
            return;
        }

        if (!password) {
            errorMsg.text("A senha é obrigatória.").show();
            return;
        }

        $.ajax({
            url: "http://localhost:8000/api/login",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({
                email,
                password
            }),
            success: function(response) {
                if (response.access_token) {
                    // Salva o token no localStorage
                    localStorage.setItem("auth_token", response.access_token);

                    // Redireciona para /charges
                    window.location.href = "/charges";
                } else {
                    errorMsg.text("Erro ao efetuar o login.").show();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response?.error) {
                    errorMsg.text(response.error).show();
                } else if (response?.errors) {
                    let firstError = Object.values(response.errors)[0][0];
                    errorMsg.text(firstError).show();
                } else {
                    errorMsg.text("Erro inesperado. Tente novamente.").show();
                }
            }
        });
    });

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
</script>

</body>
</html>
