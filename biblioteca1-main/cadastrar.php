<?php
// 1. IMPORTA A CONEXÃO COM O BANCO DE DADOS
require_once("conexao.php");

// Variável para mensagens de feedback na tela
$mensagem = "";

// 2. PROCESSA O FORMULÁRIO QUANDO FOR ENVIADO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Captura as informações digitadas
    $nome  = $_POST['nome'] ?? '';
    $login = $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Validação básica para não permitir campos vazios
    if (!empty($nome) && !empty($login) && !empty($senha)) {
        
        // Criptografia segura para a senha (padrão recomendado pelo PHP)
        $senha_cripto = password_hash($senha, PASSWORD_DEFAULT);

        // 3. QUERY COM OS NOMES EXATOS DAS SUAS COLUNAS
        $sql = "INSERT INTO usuario (nome, login, senha) VALUES (?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            
            // Vincula as variáveis à query (3 strings = "sss")
            $stmt->bind_param("sss", $nome, $login, $senha_cripto);
            
            // Executa a gravação
            if ($stmt->execute()) {
                // Mensagem de sucesso + aviso de redirecionamento automático
                $mensagem = "<p style='color: green; font-weight: bold; margin-bottom: 5px;'>Usuário cadastrado com sucesso!</p>
                             <small style='color: #666; display: block; margin-bottom: 10px;'>Redirecionando para a tela de login...</small>";
                
                // ALTERAÇÃO AQUI: Comando que faz o navegador ir para o login.php em 3 segundos
                header("Refresh: 3; url=login.php");
            } else {
                // Se der erro de login duplicado (já que a coluna 'login' é UNI - única)
                if ($conn->errno == 1062) {
                    $mensagem = "<p style='color: orange; font-weight: bold;'>Erro: Este login já está em uso por outro usuário.</p>";
                } else {
                    $mensagem = "<p style='color: red;'>Erro ao salvar no banco: " . $stmt->error . "</p>";
                }
            }
            
            $stmt->close();
        } else {
            $mensagem = "<p style='color: red;'>Erro na preparação do banco: " . $conn->error . "</p>";
        }
        
    } else {
        $mensagem = "<p style='color: orange; font-weight: bold;'>Por favor, preencha todos os campos!</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 40px; display: flex; justify-content: center; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0px 4px 10px rgba(0,0,0,0.1); width: 320px; }
        h2 { margin-top: 0; color: #333; text-align: center; }
        .campo { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input { padding: 10px; width: 100%; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { padding: 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; margin-top: 10px; }
        button:hover { background: #218838; }
        .feedback { text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Novo Usuário</h2>
        
        <!-- Exibe mensagens de erro ou sucesso -->
        <div class="feedback">
            <?php echo $mensagem; ?>
        </div>

        <form action="cadastrar.php" method="POST">
            <div class="campo">
                <label>Nome Completo:</label>
                <input type="text" name="nome" placeholder="Digite o nome" required>
            </div>

            <div class="campo">
                <label>Login (Usuário):</label>
                <input type="text" name="login" placeholder="Ex: joao.silva" required>
            </div>

            <div class="campo">
                <label>Senha:</label>
                <input type="password" name="senha" placeholder="Digite a senha" required>
            </div>

            <button type="submit">Finalizar Cadastro</button>
        </form>
    </div>

</body>
</html>
