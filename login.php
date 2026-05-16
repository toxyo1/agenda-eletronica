<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$email = "";
$erro_login = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'conexao.php';

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            
            header("Location: agenda.php");
            exit();
        } else {
            $erro_login = "Usuário ou senha inválidos.";
        }

    } catch (PDOException $erro) {
        echo "Erro inesperado: " . $erro->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <link rel="stylesheet" href="src/css/home.css">
    <link rel="stylesheet" href="src/css/login.css">
    
    <title>login</title>
</head>
<body>

    <?php include 'src/css/componentes/navbar.php'; ?>

    <main class="conteudo-login-clean">
        <div class="card-login">
            <h1>Faça seu Login</h1>
            
            <form action="login.php" method="POST">
                
                <div class="caixa-pergunta">
                    <label for="email">E-mail</label>
                    <div class="bloco-input">
                        <input type="email" id="email" name="email" class="campo-digitar" placeholder="exemplo@gmail.com" value="<?php echo htmlspecialchars($email); ?>" required>
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    
                    <?php if (!empty($erro_login)): ?>
                        <span style="color: #ef4444; font-size: 12px; margin-top: 5px; display: block; font-weight: 500;">
                            <?php echo $erro_login; ?>
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

                <p class="link-cadastro">Não tem uma conta? <a href="cadastro.php">Cadastre-se aqui.</a></p>

                <button type="submit" class="botao-entrar">
                    Entrar
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>
            </form>
        </div>
    </main>

    <?php include 'src/css/componentes/footer.php'; ?>

</body>
</html>