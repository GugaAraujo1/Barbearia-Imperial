<?php
// Incluir arquivo de configuração
require_once "config.php";

// Defina variáveis e inicialize com valores vazios
$nome = $sobrenome = $servico = $data_agendamento = $horario = "";
$nome_err = $sobrenome_err = $servico_err = $data_agendamento_err = $horario_err = "";

// Processando dados do formulário quando o formulário é enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validar nome
    if (empty(trim($_POST["nome"]))) {
        $nome_err = "Digite seu nome.";
    } else {
        $nome = trim($_POST["nome"]);
    }

    // Validar sobrenome
    if (empty(trim($_POST["sobrenome"]))) {
        $sobrenome_err = "Digite seu sobrenome.";
    } else {
        $sobrenome = trim($_POST["sobrenome"]);
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

    // Verifique os erros de entrada antes de inserir no banco de dados
    if (empty($nome_err) && empty($sobrenome_err) && empty($servico_err) && empty($data_agendamento_err) && empty($horario_err)) {

        // Prepare uma declaração de inserção
        $sql = "INSERT INTO agendamentos (nome, sobrenome, serviço, data_agendamento, horario) VALUES (:nome, :sobrenome, :servico, :data_agendamento, :horario)";

        if ($stmt = $pdo->prepare($sql)) {
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":nome", $param_nome, PDO::PARAM_STR);
            $stmt->bindParam(":sobrenome", $param_sobrenome, PDO::PARAM_STR);
            $stmt->bindParam(":servico", $param_servico, PDO::PARAM_STR);
            $stmt->bindParam(":data_agendamento", $param_data_agendamento, PDO::PARAM_STR);
            $stmt->bindParam(":horario", $param_horario, PDO::PARAM_STR);

            // Definir parâmetros
            $param_nome = $nome;
            $param_sobrenome = $sobrenome;
            $param_servico = $servico;
            $param_data_agendamento = $data_agendamento;
            $param_horario = $horario;

            // Tente executar a declaração preparada
            if ($stmt->execute()) {
                // Redirecionar para a página de login
                header("location: login.php");
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
    <title>Cadastro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Agendamento</h2>
        <p>Preencha o formulário para fazer seu agendamento!</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nome; ?>">
                <span class="invalid-feedback"><?php echo $nome_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Sobrenome</label>
                <input type="text" name="sobrenome" class="form-control <?php echo (!empty($sobrenome_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $sobrenome; ?>">
                <span class="invalid-feedback"><?php echo $sobrenome_err; ?></span>
            </div>  
            <div class="form-group">
                <label>Serviço</label>
                <select name="servico" class="form-control <?php echo (!empty($servico_err)) ? 'is-invalid' : ''; ?>">
                    <option value="Corte">Corte</option>
                    <option value="Barba">Barba</option>
                    <option value="Corte e Barba">Corte e Barba</option>
                </select>
                <span class="invalid-feedback"><?php echo $servico_err; ?></span>
            </div>  
            <div class="form-group">
                <label>Data</label>
                <input type="text" name="data_agendamento" id="datepicker" class="form-control <?php echo (!empty($data_agendamento_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $data_agendamento; ?>">
                <span class="invalid-feedback"><?php echo $data_agendamento_err; ?></span>
            </div>
            <div class="form-group">
                <label>Horário</label>
                <select name="horario" class="form-control <?php echo (!empty($horario_err)) ? 'is-invalid' : ''; ?>">
                    <?php
                    // Defina os horários disponíveis em intervalos de 30 minutos
                    $hora_inicio = strtotime('08:00');
                    $hora_fim = strtotime('19:00');

                    while ($hora_inicio <= $hora_fim) {
                        echo '<option value="' . date('H:i', $hora_inicio) . '">' . date('H:i', $hora_inicio) . '</option>';
                        $hora_inicio = strtotime('+30 minutes', $hora_inicio);
                    }
                    ?>
                </select>
                <span class="invalid-feedback"><?php echo $horario_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Agendar">
                <input type="reset" class="btn btn-secondary ml-2" value="Apagar Dados">
            </div>
            <p>Já tem uma conta? <a href="login.php">Entre aqui</a>.</p>
        </form>
    </div>

    // ...
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
            function(date) {
                // Desabilite os domingos (0 é domingo, 1 é segunda-feira, etc.)
                return (date.getDay() === 0);
            }
        ],
    });
</script>

</body>
</html>
