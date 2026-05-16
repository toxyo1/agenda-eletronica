<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="src/css/home.css">
    <title>bunny planner</title>
</head>
<body class="home-body">

    <?php include 'src/css/componentes/navbar.php'; ?>

    <main class="conteudo-home">
        
        <section class="secao-introducao">
            <div class="texto-introducao">
                <h1>Organize sua <br><span>rotina.</span></h1>
                <p>Centralize seus compromissos, estabeleça suas metas e monte a estrutura de rotina ideal para o seu dia a dia.</p>
                <a href="login.php" class="btn-comechar">
                    Começar minha jornada
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="coluna-personagem">
                <img src="src/css/img/1.jpg" alt="Personagem Bunny Planner" class="imagem-boneca">
            </div>
        </section>

        <section class="grid-beneficios">
            
            <div class="card-beneficio">
                <div class="icone-card"><i class="fa-regular fa-calendar-check"></i></div>
                <h3>Rotina Eficiente</h3>
                <p>Planeje suas tarefas diárias com um sistema visual limpo, estruturado para evitar o esquecimento e a sobrecarga mental.</p>
            </div>

            <div class="card-beneficio">
                <div class="icone-card"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <h3>Organização Diária</h3>
                <p>Registre seus compromissos, visualize seus horários e mantenha o controle dos seus prazos de forma prática.</p>
            </div>

            <div class="card-beneficio">
                <div class="icone-card"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                <h3>Seu Espaço</h3>
                <p>Organize suas prioridades do seu jeito, estruturando seu dia a dia com a clareza e a simplicidade que você precisa.</p>
            </div>

        </section>

    </main>

    <?php include 'src/css/componentes/footer.php'; ?>

</body>
</html>