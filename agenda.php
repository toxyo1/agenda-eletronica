<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include("conexao.php"); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}
$id_usuario = $_SESSION['id_usuario']; 

if (isset($_GET['excluir_id'])) {
    $stmt = $pdo->prepare("DELETE FROM atividades WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $_GET['excluir_id'], ':user_id' => $id_usuario]);
    header("Location: agenda.php");
    exit();
}

if (isset($_POST['btn_salvar'])) {
    $stmt = $pdo->prepare("INSERT INTO atividades (user_id, nome, descricao, data_inicio, data_fim, status) VALUES (:user_id, :nome, :descricao, :data_inicio, :data_fim, :status)");
    $stmt->execute([
        ':user_id'     => $id_usuario,
        ':nome'        => $_POST['nome'],
        ':descricao'   => $_POST['descricao'],
        ':data_inicio' => $_POST['data_inicio'],
        ':data_fim'    => $_POST['data_fim'],
        ':status'      => $_POST['status']
    ]);
    header("Location: agenda.php");
    exit();
}

if (isset($_POST['btn_editar'])) {
    $stmt = $pdo->prepare("UPDATE atividades SET nome = :nome, descricao = :descricao, data_inicio = :data_inicio, data_fim = :data_fim, status = :status WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        ':id'          => $_POST['id_atividade'],
        ':user_id'     => $id_usuario,
        ':nome'        => $_POST['nome'],
        ':descricao'   => $_POST['descricao'],
        ':data_inicio' => $_POST['data_inicio'],
        ':data_fim'    => $_POST['data_fim'],
        ':status'      => $_POST['status']
    ]);
    header("Location: agenda.php");
    exit();
}

$stmt = $pdo->prepare("SELECT id, nome AS title, data_inicio AS start, data_fim AS end, descricao, status FROM atividades WHERE user_id = :user_id");
$stmt->execute([':user_id' => $id_usuario]);
$eventos_json = json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bunny planner</title>
    
    <link rel="stylesheet" href="src/css/agenda.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
</head>
<body>

    <main class="conteudo-agenda">
        
        <header class="topo-agenda">
            <h1>Sua Agenda Virtual</h1>
            <p>Gerencie suas metas de forma visual sem complicação.</p>
        </header>

        <section class="grid-dashboard">
            
            <aside class="coluna-lateral">
                <div class="card-tempo-painel">
                    <p class="relogio" id="relogioJS">00:00:00</p>
                    <p class="data-hoje" id="dataJS">Carregando data...</p>
                </div>

                <div class="card-frase-ilustrada">
                    <img src="src/css/img/urso (1).png" alt="Bunny Foco" class="avatar-coelho">
                    
                    <h2 id="tituloFraseJS">Foco do Dia: Pequenos passos, grandes resultados.</h2>
                    <p id="subfraseFraseJS">Lembre-se, a consistência é a chave para os seus sonhos! Conte comigo para continuar seguindo em frente.</p>
                    
                    <div class="wrapper-botoes-frase">
                        <button class="btn-frase-acao btn-frase-confirmar" onclick="fecharCardFrase()">Vou focar!</button>
                        <button class="btn-frase-acao btn-frase-recusar" onclick="trocarFraseRapida()">Próxima</button>
                    </div>
                </div>

                <div class="area-botoes-laterais">
                    <button class="btn-lateral btn-roxo" onclick="abrirModalCadastro()">
                        <i class="fa-solid fa-plus" style="margin-right:8px;"></i> Adicionar Atividade
                    </button>
                    <a href="configuracoes.php" class="btn-lateral btn-claro">
                        <i class="fa-solid fa-gear" style="margin-right:8px;"></i> Configurações
                    </a>
                    <a href="login.php" class="btn-lateral btn-claro">
                        <i class="fa-solid fa-right-from-bracket" style="margin-right:8px;"></i> Sair da Conta
                    </a>
                </div>
            </aside>

            <article class="area-calendario-principal">
                <div id="meuCalendarioJS"></div>
            </article>

        </section>
    </main>

    <div class="modal-overlay" id="modalPrincipal">
        <div class="modal-content">
            <button class="btn-fechar-modal" onclick="fecharModal()"><i class="fa-solid fa-xmark"></i></button>
            <div id="conteudoModalDinamic"></div>
        </div>
    </div>

    <script>
        var meusEventos = <?php echo $eventos_json; ?>;

        document.addEventListener('DOMContentLoaded', function() {
            var calGrande = new FullCalendar.Calendar(document.getElementById('meuCalendarioJS'), {
                initialView: 'dayGridMonth', locale: 'pt-br', eventColor: '#7c3aed', events: meusEventos,
                headerToolbar: { left: 'prev', center: 'title', right: 'next' },
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
                dateClick: function(info) { abrirModalGerenciador(info.dateStr); },
                eventClick: function(info) { abrirModalGerenciador(info.event.startStr.split('T')[0]); }
            });
            calGrande.render();

            atualizarRelogio();
            setInterval(atualizarRelogio, 1000);
            definirFraseDoDia();
        });

        function atualizarRelogio() {
            var agora = new Date();
            var horas = String(agora.getHours()).padStart(2, '0');
            var minutos = String(agora.getMinutes()).padStart(2, '0');
            var segundos = String(agora.getSeconds()).padStart(2, '0');
            document.getElementById('relogioJS').innerText = `${horas}:${minutos}:${segundos}`;
            
            var opcoes = { weekday: 'long', day: 'numeric', month: 'long' };
            document.getElementById('dataJS').innerText = agora.toLocaleDateString('pt-BR', opcoes);
        }

        var bancoFrases = [
            { t: "Hora do Show: Menos pressa, mais ritmo.", s: "Grandes projetos são construídos uma linha por vez. Foco!" },
            { t: "Mentalidade: Feito é melhor que perfeito.", s: "Não espere as condições ideais para começar a organizar sua rotina de estudos." },
            { t: "Disciplina: O foco vence o talento.", s: "Diga não às distrações hoje para poder colher os resultados que deseja amanhã." },
            { t: "Organização: Esvazie a sua mente.", s: "Coloque suas pendências no calendário e use sua energia apenas para executar." }
        ];

        function definirFraseDoDia() {
            var hoje = new Date();
            var indice = (hoje.getDate() + hoje.getMonth()) % bancoFrases.length;
            document.getElementById('tituloFraseJS').innerText = bancoFrases[indice].t;
            document.getElementById('subfraseFraseJS').innerText = bancoFrases[indice].s;
        }

        function trocarFraseRapida() {
            var randomIndice = Math.floor(Math.random() * bancoFrases.length);
            document.getElementById('tituloFraseJS').innerText = randomIndice.t ? bancoFrases[randomIndice].t : "Foco do Dia";
            document.getElementById('subfraseFraseJS').innerText = bancoFrases[randomIndice].s;
        }

        function fecharCardFrase() {
            alert("É isso aí! Foco total ativado para hoje.");
        }

        function abrirModalCadastro() {
            document.getElementById('conteudoModalDinamic').innerHTML = `
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                    <i class="fa-solid fa-calendar-plus" style="color:#7c3aed; font-size:20px;"></i>
                    <h3 style="margin:0; font-size:18px;">Nova Atividade</h3>
                </div>
                <form action="agenda.php" method="POST">
                    <label>Nome da Atividade</label>
                    <input type="text" name="nome" placeholder="Ex: Estudar Banco de Dados" required>
                    <label>Descrição</label>
                    <textarea name="descricao" placeholder="Detalhes importantes..." style="min-height:60px; resize:none;"></textarea>
                    <div style="display:flex; gap:15px; margin-top:10px;">
                        <div style="flex:1;"><label>Início</label><input type="datetime-local" name="data_inicio" required></div>
                        <div style="flex:1;"><label>Término</label><input type="datetime-local" name="data_fim" required></div>
                    </div>
                    
                    <label style="display:block; margin-top:10px;">Status Inicial</label>
                    <select name="status" style="width:100%; padding:10px; border-radius:12px; border:1px solid #e5e7eb; font-size:14px; margin-top:5px; outline:none;">
                        <option value="andamento" selected>Pendente (Em andamento)</option>
                        <option value="concluida">Concluída</option>
                        <option value="feito">Feito</option>
                    </select>

                    <button type="submit" name="btn_salvar" class="btn-lateral btn-roxo" style="margin-top:20px; width:100%;">Salvar no Calendário</button>
                </form>
            `;
            document.getElementById('modalPrincipal').style.display = 'flex';
        }

        function abrirModalGerenciador(dataString) {
            var partes = dataString.split('-');
            var dataFormatada = partes[2] + '/' + partes[1] + '/' + partes[0];
            
            var eventosFiltrados = meusEventos.filter(ev => ev.start.startsWith(dataString));
            var listaHtml = eventosFiltrados.length === 0 
                ? `<p style="color:#9ca3af; font-size:14px; margin-top:15px;">Nenhuma atividade para este dia.</p>`
                : eventosFiltrados.map(ev => {
                    var horaTexto = ev.start.includes('T') ? ev.start.split('T')[1].substring(0, 5) : '';
                    var exibicaoTitulo = horaTexto ? `[${horaTexto}] ${ev.title}` : ev.title;

                    var textoStatus = 'PENDENTE';
                    if (ev.status === 'concluida') textoStatus = 'CONCLUÍDA';

                    var evJson = encodeURIComponent(JSON.stringify(ev));

                    return `
                        <li class="item-tarefa">
                            <div>
                                <strong style="color:#121214; display:block;">${exibicaoTitulo} (${textoStatus})</strong>
                                <span style="font-size:12px; color:#6b7280;">${ev.descricao || 'Sem descrição'}</span>
                            </div>
                            <div style="display:flex; gap:10px; align-items:center;">
                                <button type="button" style="color:#7c3aed; background:none; border:none; cursor:pointer;" onclick="abrirFormularioEdicao('${evJson}', '${dataString}')">
                                    <i class="fa-solid fa-pencil"></i>
                                </button>
                                <button class="btn-lixeira" onclick="deletar(${ev.id}, '${ev.title}')"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </li>
                    `;
                }).join('');

            document.getElementById('conteudoModalDinamic').innerHTML = `
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:5px;">
                    <i class="fa-solid fa-calendar-day" style="color:#7c3aed; font-size:20px;"></i>
                    <h3 style="margin:0; font-size:18px;">Atividades do Dia</h3>
                </div>
                <p style="margin:0 0 15px 0; font-size:14px; color:#6b7280; font-weight:500;">${dataFormatada}</p>
                <ul style="list-style:none; padding:0; margin:0;">${listaHtml}</ul>
            `;
            document.getElementById('modalPrincipal').style.display = 'flex';
        }

        function abrirFormularioEdicao(eventoCodificado, dataOriginal) {
            var ev = JSON.parse(decodeURIComponent(eventoCodificado));
            var inicioFormatado = ev.start ? ev.start.substring(0, 16) : '';
            var fimFormatado = ev.end ? ev.end.substring(0, 16) : '';

            document.getElementById('conteudoModalDinamic').innerHTML = `
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                    <button type="button" onclick="abrirModalGerenciador('${dataOriginal}')" style="background:none; border:none; color:#6b7280; cursor:pointer; font-size:16px;"><i class="fa-solid fa-arrow-left"></i></button>
                    <i class="fa-solid fa-pen-to-square" style="color:#7c3aed; font-size:18px;"></i>
                    <h3 style="margin:0; font-size:18px;">Editar Atividade</h3>
                </div>
                <form action="agenda.php" method="POST">
                    <input type="hidden" name="id_atividade" value="${ev.id}">
                    
                    <label>Nome da Atividade</label>
                    <input type="text" name="nome" value="${ev.title}" required>
                    
                    <label>Descrição</label>
                    <textarea name="descricao" style="min-height:60px; resize:none;">${ev.descricao || ''}</textarea>
                    
                    <div style="display:flex; gap:15px; margin-top:10px;">
                        <div style="flex:1;"><label>Início</label><input type="datetime-local" name="data_inicio" value="${inicioFormatado}" required></div>
                        <div style="flex:1;"><label>Término</label><input type="datetime-local" name="data_fim" value="${fimFormatado}" required></div>
                    </div>
                    
                    <label>Status da Atividade</label>
                    <select name="status" style="width:100%; padding:10px; border-radius:12px; border:1px solid #e5e7eb; font-size:14px; margin-top:5px; outline:none;">
                        <option value="andamento" ${ev.status === 'andamento' ? 'selected' : ''}>Pendente (Em andamento)</option>
                        <option value="concluida" ${ev.status === 'concluida' ? 'selected' : ''}>Concluída</option>
                        <option value="feito" ${ev.status === 'feito' ? 'selected' : ''}>Feito</option>
                    </select>
                    
                    <button type="submit" name="btn_editar" class="btn-lateral btn-roxo" style="margin-top:20px; width:100%;">Salvar Alterações</button>
                </form>
            `;
        }

        function deletar(id, nome) {
            if (confirm(`Tem certeza que deseja remover "${nome}"?`)) {
                window.location.href = "agenda.php?excluir_id=" + id;
            }
        }
        function fecharModal() { document.getElementById('modalPrincipal').style.display = 'none'; }
    </script>
</body>
</html>