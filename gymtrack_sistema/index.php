<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lógica de Login
    if (isset($_POST['login'])) {
        $email = $conn->real_escape_string($_POST['email']);
        $senha = $_POST['senha'];

        $sql_code = "SELECT * FROM usuario WHERE email = '$email'";
        $sql_query = $conn->query($sql_code) or die("Falha na execução do código SQL: " . $conn->error);

        if ($sql_query->num_rows == 1) {
            $usuario = $sql_query->fetch_assoc();
            if (password_verify($senha, $usuario['senha'])) {
                session_start();
                $_SESSION['id'] = $usuario['idusuario'];
                $_SESSION['nome'] = $usuario['nome'];
                header("Location: painel.php");
            } else {
                $login_error = "E-mail ou senha incorretos!";
            }
        } else {
            $login_error = "E-mail ou senha incorretos!";
        }
    }
    // Lógica de Cadastro
    elseif (isset($_POST['cadastrar'])) {
        $nome = $conn->real_escape_string($_POST['nome']);
        $email = $conn->real_escape_string($_POST['email']);
        $data_nascimento = $conn->real_escape_string($_POST['data_nascimento']);
        $senha = $_POST['senha'];
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Verificar se o e-mail já existe
        $check = $conn->query("SELECT idusuario FROM usuario WHERE email = '$email'");
        if ($check->num_rows > 0) {
            $cadastro_error = "Este e-mail já está cadastrado!";
        } else {
            $sql_code = "INSERT INTO usuario (nome, email, data_nascimento, senha) VALUES ('$nome', '$email', '$data_nascimento', '$senha_hash')";
            if ($conn->query($sql_code)) {
                $cadastro_success = "Cadastro realizado com sucesso! Faça o login.";
            } else {
                $cadastro_error = "Falha ao cadastrar: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gymtrack - Login e Cadastro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f0f2f5; }
        .container { max-width: 500px; margin-top: 50px; }
        .card-title { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-4">
            <h2>Bem-vindo à Gymtrack</h2>
        </div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab">Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="cadastro-tab" data-toggle="tab" href="#cadastro" role="tab">Cadastro</a>
            </li>
        </ul>
        <div class="tab-content card" id="myTabContent">
            <div class="tab-pane fade show active card-body" id="login" role="tabpanel">
                <h3 class="card-title text-center">Entrar no Sistema</h3>
                <?php if (isset($login_error)) echo "<div class='alert alert-danger'>$login_error</div>"; ?>
                <?php if (isset($cadastro_success)) echo "<div class='alert alert-success'>$cadastro_success</div>"; ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Senha</label>
                        <input type="password" name="senha" class="form-control" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
                </form>
            </div>
            <div class="tab-pane fade card-body" id="cadastro" role="tabpanel">
                <h3 class="card-title text-center">Crie sua Conta</h3>
                <?php if (isset($cadastro_error)) echo "<div class='alert alert-danger'>$cadastro_error</div>"; ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Nome Completo</label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Data de Nascimento</label>
                        <input type="date" name="data_nascimento" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Senha</label>
                        <input type="password" name="senha" class="form-control" required>
                    </div>
                    <button type="submit" name="cadastrar" class="btn btn-success btn-block">Cadastrar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
