<?php
    session_start();
    require_once "config.php";

    // Verifica se o usuário está logado como "adm"
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION["usuario"] === "adm"){
        // Seleciona apenas os agendamentos futuros do banco de dados, ordenados pela data_agendamento
        $sql = "SELECT nome, servico, DATE_FORMAT(data_agendamento, '%d/%m/%Y') as data_formatada, horario FROM agendamentos WHERE data_agendamento >= CURRENT_DATE() ORDER BY data_agendamento ASC";
        $stmt = $pdo->prepare($sql);

        if($stmt->execute()){
            $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Trate o erro, se necessário
            echo "Erro ao buscar os agendamentos.";
        }
    } else {
        // Se não for um usuário "adm", redireciona para outra página
        header("location: outra_pagina.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos.css">
    <title>Agendamentos</title>
    <style>
        .cabecalho nav ul li a.user {
            color: white;
        }
        .cabecalho nav ul a.logout {
            color: red;
        }
        .agendamentos-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 16px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .agendamentos-table th,
        .agendamentos-table td {
            padding: 15px;
            width: 15rem;
            text-align: center; /* Centralizar o conteúdo */
            border-bottom: 1px solid #ddd;
            border: 3px solid #000; /* Adicionar borda preta */
        }

        .agendamentos-table th {
            color: #f2b749; 
            background-color: black;
        }

        .agendamentos-table td {
            background-color: #f2b749; 
            color: black; 
        }

        .agendamentos-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 5rem;
        }
        .rodape {
            margin-top: auto;
        }
        .principal {
            flex-grow: 1;
        }
    </style>
</head>

<body>
    <header class="cabecalho">
        <nav>
            <ul class="ListNav">
                <li><a href="../#sobreNos">SOBRE NÓS</a></li>
                <li><a href="../#servicos">SERVIÇOS</a></li>
                <a href="../index.php">
                    <img class="Logo" src="../assets/Logo.jpeg" alt="Logo Imperial">
                </a>
                <li><a href="agendar.php">AGENDAMENTO</a></li>
                <?php
                    // Verifica se o usuário está logado
                    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
                        // Se estiver logado, mostra o nome do usuário
                        echo '<li><a class="user" id="userLink">'. strtoupper($_SESSION["usuario"]).'</a></li>';
                        // Adiciona o botão LOGOUT, inicialmente oculto
                        echo '<a href="../login/logout.php" class="logout" id="logoutLink" style="display: none;">LOGOUT</a>';
                    } else {
                        // Se não estiver logado, mostra o botão de login
                        echo '<li><a href="../login/login.php">LOGIN</a></li>';
                    }
                ?>
            </ul>
        </nav>
    </header>

    <div class="principal">
        <main>
            <div class="Titulo">
                <h1>AGENDAMENTOS</h1>
            </div>

            <div class="agendamentos-container">
                <div class="agendamentos-table">
                    <table>
                        <thead>
                            <tr>
                                <th>NOME</th>
                                <th>SERVIÇO</th>
                                <th>DATA</th>
                                <th>HORÁRIO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agendamentos as $agendamento): ?>
                                <tr>
                                    <td><?= htmlspecialchars($agendamento['nome']); ?></td>
                                    <td><?= htmlspecialchars($agendamento['servico']); ?></td>
                                    <td><?= htmlspecialchars($agendamento['data_formatada']); ?></td>
                                    <td><?= htmlspecialchars($agendamento['horario']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>


    <div class="rodape">
        <img class="Logo" src="../assets/Logo.jpeg">
        <div class="linha">
            <img class="icon" src="../assets/instagram.png" alt="">
            <p>@imperial.barbearia_</p>
        </div>
        <div class="linha">
            <img class="icon" src="../assets/whatsapp.png" alt="">
            <p>(11) 97283-1827</p>
        </div>
        <div class="linha">
            <img class="icon" src="../assets/mail.png" alt="">
            <p>imperial.barbearia@gmail.com</p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Função para alternar a visibilidade do botão de logout
            function toggleLogoutButton() {
                var logoutButton = document.getElementById('logoutLink');
                // Se o botão de logout estiver visível, oculta; se estiver oculto, mostra
                logoutButton.style.display = (logoutButton.style.display === 'none') ? 'block' : 'none';
            }

            // Adiciona um ouvinte de evento para o link do usuário
            document.getElementById('userLink').addEventListener('click', function (e) {
                e.preventDefault();
                // Chama a função para alternar a visibilidade do botão de logout
                toggleLogoutButton();
            });
        });
    </script>
</body>

</html>
