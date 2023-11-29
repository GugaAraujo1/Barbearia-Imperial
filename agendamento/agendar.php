<?php
// Incluir arquivo de configuração
require_once "config.php";

// Inicialize a sessão
session_start();

// Defina variáveis e inicialize com valores vazios
$nome = $servico = $data_agendamento = $horario = "";
$nome_err = $servico_err = $data_agendamento_err = $horario_err = "";

// Verificar se o usuário está logado
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    // Se estiver logado, definir o valor padrão para o campo "nome"
    $nome = $_SESSION["nome"];
}

// Processando dados do formulário quando o formulário é enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validar nome
    if (empty(trim($_POST["nome"]))) {
        $nome_err = "Digite seu nome.";
    } else {
        $nome = trim($_POST["nome"]);
    }

    // Validar serviço
    if (empty(trim($_POST["servico"]))) {
        $servico_err = "Selecione o serviço";
    } else {
        $servico = trim($_POST["servico"]);
    }

    // Validar data de agendamento
    if (empty(trim($_POST["data_agendamento"]))) {
        $data_agendamento_err = "Selecione a data de agendamento";
    } else {
        $data_agendamento = trim($_POST["data_agendamento"]);
    }

    // Validar horário
    if (empty(trim($_POST["horario"]))) {
        $horario_err = "Selecione o horário";
    } else {
        $horario = trim($_POST["horario"]);
    }

    $sql_verificar_disponibilidade = "SELECT id_agendamento FROM agendamentos WHERE data_agendamento = :data_agendamento AND horario = :horario";

    if ($stmt_verificar = $pdo->prepare($sql_verificar_disponibilidade)) {
        $stmt_verificar->bindParam(":data_agendamento", $data_agendamento, PDO::PARAM_STR);
        $stmt_verificar->bindParam(":horario", $horario, PDO::PARAM_STR);

        if ($stmt_verificar->execute()) {
            if ($stmt_verificar->rowCount() > 0) {
                $horario_err = "Este horário já está ocupado para a data selecionada.";
            }
        } else {
            echo "Ops! Algo deu errado ao verificar a disponibilidade. Por favor, tente novamente mais tarde.";
        }

        unset($stmt_verificar);
    }

    // Verifique os erros de entrada antes de inserir no banco de dados
    if (empty($nome_err) && empty($servico_err) && empty($data_agendamento_err) && empty($horario_err)) {

        // Prepare uma declaração de inserção
        $sql = "INSERT INTO agendamentos (nome, servico, data_agendamento, horario) VALUES (:nome, :servico, :data_agendamento, :horario)";

        if ($stmt = $pdo->prepare($sql)) {
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":nome", $param_nome, PDO::PARAM_STR);
            $stmt->bindParam(":servico", $param_servico, PDO::PARAM_STR);
            $stmt->bindParam(":data_agendamento", $param_data_agendamento, PDO::PARAM_STR);
            $stmt->bindParam(":horario", $param_horario, PDO::PARAM_STR);

            // Definir parâmetros
            $param_nome = $nome;
            $param_servico = $servico;
            $param_data_agendamento = $data_agendamento;
            $param_horario = $horario;

            // Tente executar a declaração preparada
            if ($stmt->execute()) {
                // Redirecionar para a página de login
                header("location: ../index.php");
                exit(); // Certifique-se de sair após o redirecionamento
            } else {
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }

            // Fechar declaração
            unset($stmt);
        }
    }
}

// Fechar conexão
unset($pdo);
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Agendamento</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="../estilos.css">
    <style>
        .cabecalho nav ul li a.user {
            color: white;
        }
        .cabecalho nav ul a.logout {
            color: red;
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
                <h1>FAÇA SEU AGENDAMENTO!</h1>
            </div>

            <div class="horarios">
                <div class="horario">
                    <h2 class="dia">DOM</h2>
                    <p>FECHADO</p>
                </div>
                <div class="horario">
                    <h2 class="dia">SEG</h2>
                    <p>8H ÀS 19H</p>
                </div>
                <div class="horario">
                    <h2 class="dia">TER</h2>
                    <p>8H ÀS 19H</p>
                </div>
                <div class="horario">
                    <h2 class="dia">QUA</h2>
                    <p>8H ÀS 19H</p>
                </div>
                <div class="horario">
                    <h2 class="dia">QUI</h2>
                    <p>8H ÀS 19H</p>
                </div>
                <div class="horario">
                    <h2 class="dia">SEX</h2>
                    <p>8H ÀS 19H</p>
                </div>
                <div class="horario">
                    <h2 class="dia">SAB</h2>
                    <p>8H ÀS 19H</p>
                </div>
            </div>
            <div class="wrapper">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <p class="tituloForm">DIGITE SEU NOME</p>
                    <input type="text" name="nome" class="form-control <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nome; ?>">
                    <span class="invalid-feedback">
                        <?php echo $nome_err; ?>
                    </span>
                </div>
                    <div class="form-group">
                        <p class="tituloForm">SELECIONE O SERVIÇO</p>
                        <select name="servico"
                            class="form-control <?php echo (!empty($servico_err)) ? 'is-invalid' : ''; ?>">
                            <option value=""></option>
                            <option value="Corte">Corte</option>
                            <option value="Barba">Barba</option>
                            <option value="Corte e Barba">Corte e Barba</option>
                        </select>
                        <span class="invalid-feedback">
                            <?php echo $servico_err; ?>
                        </span>
                    </div>
                    <div class="data">
                        <div class="form-group">
                            <p class="tituloForm">DATA</p>
                            <input type="text" name="data_agendamento" id="datepicker"
                                class="form-control <?php echo (!empty($data_agendamento_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $data_agendamento; ?>">
                            <span class="invalid-feedback">
                                <?php echo $data_agendamento_err; ?>
                            </span>
                        </div>
                        <div class="form-group">
                            <p class="tituloForm">HORÁRIO</p>
                            <select name="horario"
                                class="form-control <?php echo (!empty($horario_err)) ? 'is-invalid' : ''; ?>">
                                <option value=""></option>
                                <?php
                                $hora_inicio = strtotime('08:00');
                                $hora_fim = strtotime('19:00');
                                while ($hora_inicio <= $hora_fim) {
                                    echo '<option value="' . date('H:i', $hora_inicio) . '">' . date('H:i', $hora_inicio) . '</option>';
                                    $hora_inicio = strtotime('+30 minutes', $hora_inicio);
                                }
                                ?>
                            </select>
                            <span class="invalid-feedback">
                                <?php echo $horario_err; ?>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary btn-confirmar" value="CONFIRMAR AGENDAMENTO">

                    </div>
                </form>
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


    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        // Inicializar o seletor personalizado
        $("select[name='servico']").select2();

        // Obtém a data atual
        var currentDate = new Date();

        // Configurar o flatpickr
        flatpickr("#datepicker", {
            dateFormat: "Y-m-d",
            minDate: currentDate, // Restringir a data mínima à data atual
            disable: [
                function (date) {
                    // Desabilite os domingos (0 é domingo, 1 é segunda-feira, etc.)
                    return (date.getDay() === 0);
                }
            ],
        });
    </script>
    
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Função para redirecionar para a página adequada ao clicar no nome do usuário
            function redirectToPage() {
                // Verifica se o usuário é "adm" e redireciona para a página correta
                if ("<?php echo $_SESSION["usuario"]; ?>" === "adm") {
                    window.location.href = "agendamentos.php";
                } 
            }

            // Adiciona um ouvinte de evento para o link do usuário
            document.getElementById('userLink').addEventListener('click', function (e) {
                e.preventDefault();
                // Chama a função para redirecionar para a página adequada
                redirectToPage();
            });
        });
    </script>

</body>

</html>