<?php
// Incluir arquivo de configuração
require_once "config.php";
 
// Defina variáveis e inicialize com valores vazios
$nome = $usuario = $senha = $confirmar_senha = "";
$nome_err = $usuario_err = $senha_err = $confirmar_senha_err = "";
 
// Processando dados do formulário quando o formulário é enviado
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validar nome
    if (empty(trim($_POST["nome"]))) {
        $nome_err = "Digite seu nome.";
    } else {
       // Prepare uma declaração selecionada
       $sql = "SELECT id FROM usuarios WHERE nome = :nome";
        
       if($stmt = $pdo->prepare($sql)){
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":nome", $param_nome, PDO::PARAM_STR);
            
            // Definir parâmetros
            $param_nome = trim($_POST["nome"]);
            
            // Tente executar a declaração preparada
            if($stmt->execute()){
                $nome = trim($_POST["nome"]);
            } else{
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }

            // Fechar declaração
            unset($stmt);
        }
    }

    // Validar nome de usuário
    if(empty(trim($_POST["usuario"]))){
        $usuario_err = "Por favor coloque um nome de usuário.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["usuario"]))){
        $usuario_err = "O nome de usuário pode conter apenas letras, números e sublinhados.";
    } else{
        // Prepare uma declaração selecionada
        $sql = "SELECT id FROM usuarios WHERE usuario = :usuario";
        
        if($stmt = $pdo->prepare($sql)){
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":usuario", $param_usuario, PDO::PARAM_STR);
            
            // Definir parâmetros
            $param_usuario = trim($_POST["usuario"]);
            
            // Tente executar a declaração preparada
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $usuario_err = "Este nome de usuário já está em uso.";
                } else{
                    $usuario = trim($_POST["usuario"]);
                }
            } else{
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }

            // Fechar declaração
            unset($stmt);
        }
    }
    
    // Validar senha
    if(empty(trim($_POST["senha"]))){
        $senha_err = "Por favor insira uma senha.";     
    } elseif(strlen(trim($_POST["senha"])) < 6){
        $senha_err = "A senha deve ter pelo menos 6 caracteres.";
    } else{
        $senha = trim($_POST["senha"]);
    }
    
    // Validar e confirmar a senha
    if(empty(trim($_POST["confirmar_senha"]))){
        $confirmar_senha_err = "Por favor, confirmare a senha.";     
    } else{
        $confirmar_senha = trim($_POST["confirmar_senha"]);
        if(empty($senha_err) && ($senha != $confirmar_senha)){
            $confirmar_senha_err = "A senha não confere.";
        }
    }
    
    // Verifique os erros de entrada antes de inserir no banco de dados
    if(empty($usuario_err) && empty($senha_err) && empty($confirmar_senha_err)){
        
        // Prepare uma declaração de inserção
        $sql = "INSERT INTO usuarios (nome, usuario, senha) VALUES (:nome, :usuario, :senha)";
         
        if($stmt = $pdo->prepare($sql)){
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":nome", $param_nome, PDO::PARAM_STR);
            $stmt->bindParam(":usuario", $param_usuario, PDO::PARAM_STR);
            $stmt->bindParam(":senha", $param_senha, PDO::PARAM_STR);

            // Definir parâmetros
            $param_nome = $nome;
            $param_usuario = $usuario;
            $param_senha = password_hash($senha, PASSWORD_DEFAULT);

            // Tente executar a declaração preparada
            if($stmt->execute()){
                // Obter o ID gerado
                $last_id = $pdo->lastInsertId();
            
                // Consultar o banco de dados para obter o nome correspondente ao ID
                $sql_select_name = "SELECT nome FROM usuarios WHERE id = :id";
                $stmt_select_name = $pdo->prepare($sql_select_name);
                $stmt_select_name->bindParam(":id", $last_id, PDO::PARAM_INT);
                if ($stmt_select_name->execute()) {
                    if ($row_name = $stmt_select_name->fetch()) {
                        $_SESSION["nome"] = $row_name["nome"];
                    }
                }
            
                // Redirecionar para a página de login
                header("location: login.php");
            } else {
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }

            // Fechar declaração
            unset($stmt);
        }
    }
    
    // Fechar conexão
    unset($pdo);
}
?>
 
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../estilos.css">
    <style>
        .principal p{
            font-size: 0.7rem;
            text-align: center;
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
                <li><a href="../agendamento/agendar.php">AGENDAMENTO</a></li>
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
    <div class="principalLogin">
        <div class="wrapper">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <p>SEU NOME</p>
                    <input type="text" name="nome" class="form-control <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nome; ?>"style="width: 40rem; height: 3rem; border-radius: 0.7rem;">
                    <span class="invalid-feedback"><?php echo $nome_err; ?></span>
                </div>
                <div class="form-group">
                    <p>USUÁRIO</p>
                    <input type="text" name="usuario" class="form-control <?php echo (!empty($usuario_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $usuario; ?>"style="width: 40rem; height: 3rem; border-radius: 0.7rem;">
                    <span class="invalid-feedback"><?php echo $usuario_err; ?></span>
                </div>    
                <div class="form-group">
                    <p>SENHA</p>
                    <input type="password" name="senha" class="form-control <?php echo (!empty($senha_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $senha; ?>"style="width: 40rem; height: 3rem; border-radius: 0.7rem;">
                    <span class="invalid-feedback"><?php echo $senha_err; ?></span>
                </div>
                <div class="form-group">
                    <p>CONFIRME A SENHA</p>
                    <input type="password" name="confirmar_senha" class="form-control <?php echo (!empty($confirmar_senha_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirmar_senha; ?>"style="width: 40rem; height: 3rem; border-radius: 0.7rem;">
                    <span class="invalid-feedback"><?php echo $confirmar_senha_err; ?></span>
                </div>
                <div class="botoes">
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="CRIAR CONTA" style="width: 20rem; height: 4rem;">
                    </div>
                    <div class="entreaqui">
                        
                        <a class="secundario" href="login.php"  style="width: 15rem; height: 4rem;">JÁ TENHO UMA CONTA!</a>
                    </div>
                </div>
            </form>
        </div>    
    </div>
</body>
</html>