<?php 

    session_start();

    if(!$_SESSION['email']){
        header('Location: ../login.php');
    }

    include "../conexao.php";

    $id = $_GET['id'];

    $leitor = $conn->query("SELECT * FROM leitor WHERE id = $id")->fetch_assoc();

    if(isset($_POST['email'])){

        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $sql = "UPDATE leitor SET nome = '$nome', email = '$email', senha = '$senha' WHERE id = $id";

        $resultado = $conn->query($sql);

        header('Location: listar.php');

    }


?>

<h1>Atualizar Leitor</h1>
<a href="listar.php">voltar</a>
<br> <br>

<form method="post">
    <input type="text" value="<?= $leitor['nome'] ?>" name="nome" placeholder="Nome do leitor" id="">
    <input type="email" value="<?= $leitor['email'] ?>" name="email" placeholder="Exemple@email.com" id="">
    <input type="password" value="<?= $leitor['senha'] ?>" name="senha" placeholder="Senha" id="">
    <button type="submit">Atualizar</button>
</form>