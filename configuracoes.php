<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conexao.php"); 

$id_usuario = $_SESSION['id_usuario'] ?? 1; 
$mensagem = "";

try {
    $sql_busca = "SELECT nome, email FROM usuario WHERE id = :id";
    $stmt = $pdo->prepare($sql_busca);
    $stmt->execute(['id' => $id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao buscar dados: " . $e->getMessage();
}

if (isset($_POST['btn_atualizar'])) {
    $novo_nome = $_POST['nome'];
    $novo_email = $_POST['email'];

    try {
        $sql_update = "UPDATE usuario SET nome = :nome, email = :email WHERE id = :id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([
            'nome' => $novo_nome,
            'email' => $novo_email,
            'id' => $id_usuario
        ]);

        $usuario['nome'] = $novo_nome;
        $usuario['email'] = $novo_email;
        $mensagem = "Informações atualizadas com sucesso!";
    } catch (PDOException $e) {
        $mensagem = "Erro ao atualizar: " . $e->getMessage();
    }
}

if (isset($_POST['btn_excluir'])) {
    try {
        $pdo->prepare("DELETE FROM atividades WHERE user_id = :id")->execute(['id' => $id_usuario]);
        $pdo->prepare("DELETE FROM usuario WHERE id = :id")->execute(['id' => $id_usuario]);
        session_destroy();
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        echo "Erro ao excluir conta: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Bunny Planner</title>
    
    <link rel="stylesheet" href="src/css/agenda.css"> 
    <link rel="stylesheet" href="src/css/cadastro.css">
    <link rel="stylesheet" href="src/css/configuracoes.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php 
        if (file_exists('src/css/componentes/header.php')) {
            include 'src/css/componentes/header.php';
        } elseif (file_exists('header.php')) {
            include 'header.php';
        }
    ?>

    <main class="conteudo-cadastro-clean">
        
        <div class="container-config-com-voltar">
            
            <a href="agenda.php" class="btn-voltar-pagina">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
            
            <div class="card-cadastro">
                
                <h1>Editar Perfil</h1>

                <?php if($mensagem): ?>
                    <div class="alerta-sucesso-config">
                        <i class="fa-solid fa-circle-check"></i> <?php echo $mensagem; ?>
                    </div>
                <?php endif; ?>

                <form action="configuracoes.php" method="POST">
                    <div class="caixa-pergunta">
                        <label>Seu Nome Atual</label>
                        <div class="bloco-input">
                            <input type="text" name="nome" class="campo-digitar" value="<?php echo htmlspecialchars($usuario['nome'] ?? ''); ?>" required>
                            <i class="fa-solid fa-user"></i>
                        </div>
                    </div>

                    <div class="caixa-pergunta">
                        <label>Seu E-mail Atual</label>
                        <div class="bloco-input">
                            <input type="email" name="email" class="campo-digitar" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" required>
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                    </div>

                    <button type="submit" name="btn_atualizar" class="botao-cadastrar">
                        <i class="fa-solid fa-check"></i> Salvar Alterações
                    </button>
                </form>

                <hr class="divisor-config">

                <form action="configuracoes.php" method="POST" onsubmit="return confirm('Tem certeza que deseja apagar sua conta? Todas as suas tarefas serão perdidas definitivamente.');">
                    <button type="submit" name="btn_excluir" class="botao-excluir-conta">
                        <i class="fa-solid fa-trash-can"></i> Excluir Conta
                    </button>
                </form>
                
            </div>
        </div>
    </main>
</body>
</html>