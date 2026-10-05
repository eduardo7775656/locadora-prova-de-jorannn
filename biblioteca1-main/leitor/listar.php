<h1>Leitores</h1>
<a href="inserir.php">+ Novo leitor</a>
<br><br>
<a href="/biblioteca">voltar</a>
<br> <br>

<?php 

    session_start();

    if(!$_SESSION['email']){
        header('Location: ../login.php');
    }

    include "../conexao.php";
    
    $sql = "SELECT * FROM leitor";

    $resultado = $conn->query($sql);

    if($resultado->num_rows > 0){
        foreach($resultado as $leitor){
            echo $leitor['nome'];
            echo "<a href='atualizar.php?id=".$leitor['id']."'> editar</a>";
            echo " | ";
            echo "<a href='apagar.php?id=".$leitor['id']."'> excluir</a>";
            echo "<br>";
        }


    }else{
        echo "Nenhum resultado encontrado!";
    }

?>