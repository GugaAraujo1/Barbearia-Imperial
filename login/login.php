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
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Por favor, preencha os campos para fazer o login.</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nome do usuário</label>
                <input type="text" name="usuario" class="form-control <?php echo (!empty($usuario_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $usuario; ?>">
                <span class="invalid-feedback"><?php echo $usuario_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Senha</label>
                <input type="senha" name="senha" class="form-control <?php echo (!empty($senha_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $senha_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Entrar">
            </div>
            <p>Não tem uma conta? <a href="cadastro.php">Inscreva-se agora</a>.</p>
        </form>
    </div>
</body>
</html>