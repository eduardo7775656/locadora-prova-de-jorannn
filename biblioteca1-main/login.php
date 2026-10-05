<?php
// 1. INICIA A SESSÃO DO PHP
session_start();

// Se o usuário já estiver logado, manda direto para a página principal
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// 2. IMPORTA A CONEXÃO COM O BANCO DE DADOS
require_once("conexao.php");

$mensagem = "";

// 3. PROCESSA O LOGIN QUANDO ENVIADO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!empty($login) && !empty($senha)) {
        
        // Busca o usuário baseado no campo 'login' da sua tabela 'usuario'
        $sql = "SELECT id_usuario, nome, senha, nivel, ativo FROM usuario WHERE login = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            if ($resultado->num_rows === 1) {
                $usuario = $resultado->fetch_assoc();
                
                // Verifica se a conta está ativa
                if ($usuario['ativo'] == 0) {
                    $mensagem = "Esta conta está desativada.";
                } 
                // Verifica a senha criptografada
                elseif (password_verify($senha, $usuario['senha'])) {
                    
                    // Salva os dados na Sessão para usar nas páginas internas
                    $_SESSION['usuario_id']    = $usuario['id_usuario'];
                    $_SESSION['usuario_nome']  = $usuario['nome'];
                    $_SESSION['usuario_nivel'] = $usuario['nivel'];
                    
                    // REDIRECIONAMENTO COM SUCESSO PARA O INDEX
                    header("Location: index.php");
                    exit;
                    
                } else {
                    $mensagem = "Senha incorreta!";
                }
            } else {
                $mensagem = "Usuário não encontrado!";
            }
            $stmt->close();
        } else {
            $mensagem = "Erro no sistema: " . $conn->error;
        }
    } else {
        $mensagem = "Preencha todos os campos!";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LocadoraGestão</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #0b0c16; margin: 0; padding: 40px; display: flex; justify-content: center; align-items: center; height: 90vh; }
        .container { background: white; padding: 40px; border-radius: 20px; box-shadow: 0px 10px 30px rgba(0,0,0,0.3); width: 400px; text-align: center; }
        .logo-box { background: #5d3bf8; width: 60px; height: 60px; border-radius: 15px; display: flex; justify-content: center; align-items: center; margin: 0 auto 20px; color: white; font-size: 24px; }
        h2 { margin: 0; color: #1e1e2f; font-size: 26px; }
        h2 span { color: #5d3bf8; }
        p.subtitle { color: #888; font-size: 14px; margin-top: 5px; margin-bottom: 30px; }
        .campo { margin-bottom: 20px; text-align: left; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 12px; letter-spacing: 0.5px; }
        .input-wrapper { display: flex; align-items: center; background: #f4f5f9; border-radius: 10px; padding: 12px; border: 1px solid #e1e2e7; }
        .input-wrapper input { border: none; background: transparent; width: 100%; outline: none; font-size: 15px; color: #333; margin-left: 10px; }
        .btn-entrar { background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: white; border: none; padding: 15px; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; margin-top: 10px; transition: 0.3s; display: flex; justify-content: center; align-items: center; gap: 10px; }
        .btn-entrar:hover { opacity: 0.9; transform: translateY(-1px); }
        .link-footer { margin-top: 25px; font-size: 14px; color: #666; }
        .link-footer a { color: #5d3bf8; text-decoration: none; font-weight: bold; }
        .erro-alerta { color: #ff3b30; font-weight: bold; font-size: 14px; margin-bottom: 15px; background: #ffe5e5; padding: 10px; border-radius: 8px; }
    </style>
</head>
<body>

    <div class="container">
        <div class="logo-box">🚗</div>
        
        <h2>Locadora<span>Gestão</span></h2>
        <p class="subtitle">Insira suas credenciais para acessar o painel</p>

        <?php if (!empty($mensagem)): ?>
            <div class="erro-alerta"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="campo">
                <label>USUÁRIO OU E-MAIL</label>
                <div class="input-wrapper">
                    👤 <input type="text" name="login" placeholder="Ex: joao.silva" required>
                </div>
            </div>

            <div class="campo">
                <label>SENHA</label>
                <div class="input-wrapper">
                    🔒 <input type="password" name="senha" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-entrar">Acessar Sistema →</button>
        </form>

        <div class="link-footer">
            Novo por aqui? <a href="cadastrar.php">Crie uma conta gratuita</a>
        </div>
    </div>

</body>
</html>
