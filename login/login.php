<?php
// Inicialize a sessão
session_start();
 
// Incluir arquivo de configuração
require_once "config.php";
 
// Defina variáveis e inicialize com valores vazios
$nome = $usuario = $senha = "";
$nome_err = $usuario_err = $senha_err = $login_err = "";
 
// Processando dados do formulário quando o formulário é enviado
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Verifique se o nome de usuário está vazio
    if(empty(trim($_POST["usuario"]))){
        $usuario_err = "Por favor, insira o nome de usuário.";
    } else{
        $usuario = trim($_POST["usuario"]);
    }
    
    // Verifique se a senha está vazia
    if(empty(trim($_POST["senha"]))){
        $senha_err = "Por favor, insira sua senha.";
    } else{
        $senha = trim($_POST["senha"]);
    }
    
    // Validar credenciais
    if(empty($usuario_err) && empty($senha_err)){
        // Prepare uma declaração selecionada
        $sql = "SELECT id, usuario, senha, nome FROM usuarios WHERE usuario = :usuario";

        if($stmt = $pdo->prepare($sql)){
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":usuario", $param_usuario, PDO::PARAM_STR);
            
            // Definir parâmetros
            $param_usuario = trim($_POST["usuario"]);
            
            // Tente executar a declaração preparada
            if($stmt->execute()){
                // Verifique se o nome de usuário existe, se sim, verifique a senha
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $usuario = $row["usuario"];
                        $hashed_senha = $row["senha"];
                        if(password_verify($senha, $hashed_senha)){
                            // A senha está correta, então inicie uma nova sessão
                            session_start();
                            
                            // Armazene dados em variáveis de sessão
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["usuario"] = $usuario;       
                            $_SESSION["nome"] = $row["nome"];                     
                            
                            // Redirecionar o usuário para a página de boas-vindas
                            header("location: ../index.php");
                        } else{
                            // A senha não é válida, exibe uma mensagem de erro genérica
                            $login_err = "Nome de usuário ou senha inválidos.";
                        }
                    }
                } else{
                    // O nome de usuário não existe, exibe uma mensagem de erro genérica
                    $login_err = "Nome de usuário ou senha inválidos.";
                }
            } else{
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
        <title>Login</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="../estilos.css">
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
                <?php 
                if(!empty($login_err)){
                    echo '<div class="alert alert-danger">' . $login_err . '</div>';
                }        
                ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <h2>USUÁRIO</h2>
                        <input type="text" name="usuario" class="form-control <?php echo (!empty($usuario_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $usuario; ?>" style="width: 50rem; height: 4rem; border-radius: 0.7rem;">
                        <span class="invalid-feedback"><?php echo $usuario_err; ?></span>
                    </div>    
                    <div class="form-group">
                        <h2>SENHA</h2>
                        <input type="password" name="senha" class="form-control <?php echo (!empty($senha_err)) ? 'is-invalid' : ''; ?>" style="width: 50rem; height: 4rem; border-radius: 0.7rem;">
                        <span class="invalid-feedback"><?php echo $senha_err; ?></span>
                    </div>
                    <div class="botoes">
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="ENTRAR" style="width: 20rem; height: 4rem;">
                        </div>
                        <a class="secundario" href="cadastro.php" style="width: 15rem; height: 4rem;">CRIAR UMA CONTA NOVA!</a>
                    </div>

                </form>
            </div>
        </div>
    </body>
</html>