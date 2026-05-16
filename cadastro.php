<?php
// Variável para guardar a mensagem de erro de e-mail já utilizado.
$erro_email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'conexao.php';

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO usuario (nome, email, senha) VALUES (:nome, :email, :senha)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->execute();

        header("Location: cadastro.php");
        exit();
    } catch (PDOException $erro) {
        if ($erro->getCode() == 23000) {
            $erro_email = "Esse e-mail já está sendo utilizado.";
        } else {
            echo "Erro inesperado: " . $erro->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="src/css/style.css">
    <title>Cadastro de Usuário</title>
</head>
<body>

    <main class="card-cadastro">
        <h1>Cadastro de Usuário</h1>

        <form action="cadastro.php" method="POST">
            
            <div class="caixa-pergunta">
                <label for="nome">Nome</label>
                <div class="bloco-input">
                    <input type="text" id="nome" name="nome" class="campo-digitar" placeholder="Digite seu nome completo" required>
                    <i class="fa-regular fa-user"></i>
                </div>
            </div>

            <div class="caixa-pergunta">
                <label for="email">E-mail</label>
                <div class="bloco-input">
                    <input type="email" id="email" name="email" class="campo-digitar" placeholder="exemplo@gmail.com" required>
                    <i class="fa-regular fa-envelope"></i>
                </div>
                
                <?php if (!empty($erro_email)): ?>
                    <span style="color: #ef4444; font-size: 12px; margin-top: 5px; display: block; font-weight: 500;">
                        <?php echo $erro_email; ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="caixa-pergunta">
                <label for="senha">Senha</label>
                <div class="bloco-input">
                    <input type="password" id="senha" name="senha" class="campo-digitar" placeholder="*******" required>
                    <i class="fa-regular fa-eye-slash"></i>
                </div>
            </div>

            <p class="link-login">
                Já tem conta? Faça <a href="login.php">login</a> aqui.
            </p>
            <button type="submit" class="botao-cadastrar">
                <i class="fa-solid fa-check"></i>
                Cadastrar
            </button>
        </form>
    </main>

</body>
</html>