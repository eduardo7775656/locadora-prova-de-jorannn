<?php 

    session_start();

    if(!$_SESSION['email']){
        header('Location: ../login.php');
    }

    include "../conexao.php";

    if(isset($_POST['email'])){

        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $sql = "INSERT INTO leitor(nome, email, senha) VALUES('$nome','$email', '$senha')";

        $resultado = $conn->query($sql);

        header('Location: listar.php');

    }


?>

<h1>Adicionar Leitor</h1>
<a href="listar.php">voltar</a>
<br> <br>

<form method="post">
    <input type="text" name="nome" placeholder="Nome do leitor" id="">
    <input type="email" name="email" placeholder="Exemple@email.com" id="">
    <input type="password" name="senha" placeholder="Senha" id="">
    <button type="submit">Cadastrar</button>
</form>