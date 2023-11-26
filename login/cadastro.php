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
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Cadastro</h2>
        <p>Por favor, preencha este formulário para criar uma conta.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Seu Nome</label>
                <input type="text" name="nome" class="form-control <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nome; ?>">
                <span class="invalid-feedback"><?php echo $nome_err; ?></span>
            </div>
            <div class="form-group">
                <label>Nome do usuário</label>
                <input type="text" name="usuario" class="form-control <?php echo (!empty($usuario_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $usuario; ?>">
                <span class="invalid-feedback"><?php echo $usuario_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Senha</label>
                <input type="senha" name="senha" class="form-control <?php echo (!empty($senha_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $senha; ?>">
                <span class="invalid-feedback"><?php echo $senha_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirme a senha</label>
                <input type="senha" name="confirmar_senha" class="form-control <?php echo (!empty($confirmar_senha_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirmar_senha; ?>">
                <span class="invalid-feedback"><?php echo $confirmar_senha_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Criar Conta">
                <input type="reset" class="btn btn-secondary ml-2" value="Apagar Dados">
            </div>
            <p>Já tem uma conta? <a href="login.php">Entre aqui</a>.</p>
        </form>
    </div>    
</body>
</html>