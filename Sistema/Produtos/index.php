<?php

//print_r($_POST);


$txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
$txtNome = (isset($_POST['txtNome'])) ? $_POST['txtNome'] : "";
$txtDescricao = (isset($_POST['txtDescricao'])) ? $_POST['txtDescricao'] : "";
$txtFoto = (isset($_FILES['txtFoto']["name"])) ? $_FILES['txtFoto'] : "";

$action = (isset($_POST['action'])) ? $_POST['action'] : "";

include("../conexao/conexao.php");

$actionAdd = "";
$actionMod = $actionDel = $actionCan = "disabled";
$mostrarModal = false;

switch ($action) {
    case "btnAdicionar":

        $sentencia = $pdo->prepare("INSERT INTO produtos(Nome, Descricao, Foto)
            VALUES (:Nome, :Descricao, :Foto)");

        $sentencia->bindParam(':Nome', $txtNome);
        $sentencia->bindParam(':Descricao', $txtDescricao);

        $Fecha = new DateTime();
        $nomeArquivo = ($txtFoto != "") ? $Fecha->getTimestamp() . "_" . $_FILES["txtFoto"]["name"] : "imagem.jpg";

        $tmpFoto = $_FILES["txtFoto"]["tmp_name"];

        if ($tmpFoto != "") {
            move_uploaded_file($tmpFoto, "../Imagens/" . $nomeArquivo);
        }



        $sentencia->bindParam(':Foto', $nomeArquivo);
        $sentencia->execute();

        header('Location: index.php');

        break;
    case "btnModificar":

        $sentencia = $pdo->prepare("UPDATE produtos SET 
        Nome=:Nome,
        Descricao=:Descricao WHERE id=:id");


        $sentencia->bindParam(':Nome', $txtNome);
        $sentencia->bindParam(':Descricao', $txtDescricao);

        $sentencia->bindParam(':id', $txtID);
        $sentencia->execute();

        $Fecha = new DateTime();
        $nomeArquivo = ($txtFoto != "") ? $Fecha->getTimestamp() . "_" . $_FILES["txtFoto"]["name"] : "imagem.jpg";

        $tmpFoto = $_FILES["txtFoto"]["tmp_name"];

        if ($tmpFoto != "") {
            move_uploaded_file($tmpFoto, "../Imagens/" . $nomeArquivo);

            $sentencia = $pdo->prepare(" SELECT foto FROM produtos WHERE id=:id");
            $sentencia->bindParam(':id', $txtID);
            $sentencia->execute();

            $produtos = $sentencia->fetch(PDO::FETCH_LAZY);
            print_r($produtos);

            if (isset($produtos["Foto"])) {
                if (file_exists("../Imagens/" . $produtos["Foto"])) {

                    if ($item['Foto'] != "imagem.jpg") {
                        unlink("../Imagens/" . $produtos["Foto"]);
                    }
                }
            }

            $sentencia = $pdo->prepare("UPDATE produtos SET Foto=:Foto WHERE id=:id");
            $sentencia->bindParam(':Foto', $nomeArquivo);
            $sentencia->bindParam(':id', $txtID);
            $sentencia->execute();
        }



        header('Location: index.php');

        /*  echo $txtID;
        echo "Você btnModificar"; */
        break;
    case "btnEliminar":

        $sentencia = $pdo->prepare(" SELECT foto FROM produtos WHERE id=:id");
        $sentencia->bindParam(':id', $txtID);
        $sentencia->execute();

        $produtos = $sentencia->fetch(PDO::FETCH_LAZY);
        print_r($produtos);

        if (isset($produtos["Foto"]) && ($item['Foto'] != "imagem.jpg")) {
            if (file_exists("../Imagens/" . $produtos["Foto"])) {
                unlink("../Imagens/" . $produtos["Foto"]);
            }
        }


        $sentencia = $pdo->prepare(" DELETE FROM produtos WHERE id=:id");
        $sentencia->bindParam(':id', $txtID);
        $sentencia->execute();
        header('Location: index.php');


        /* echo $txtID;
        echo "Você btnEliminar"; */
        break;
    case "btnCancelar":
        header('Location: index.php');

        break;
    case "Selecionar":
        $actionAdd = "disabled";
        $actionMod = $actionDel = $actionCan = "";
        $mostrarModal = true;

        $sentencia = $pdo->prepare(" SELECT * FROM produtos WHERE id=:id");
        $sentencia->bindParam(':id', $txtID);
        $sentencia->execute();
        $produtos = $sentencia->fetch(PDO::FETCH_LAZY);

        $txtNome=$produto['Nome'];
        $txtDescricao=$produto['descricao'];  
        $txtFoto=$produto['Foto']; 
       



        break;
}

$sentencia = $pdo->prepare("SELECT * FROM `produtos` WHERE 1");
$sentencia->execute();
$listaProdutos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

//print_r($listaProdutos);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD COM PHP</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js"></script>

</head>

<body>

    <div class="container">

        <form action="" method="post" enctype="multipart/form-data">



            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"> PRODUTOS </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-row">
                                <input type="hidden" required name="txtID" value="<?php echo $txtID; ?>" placeholder="" id="txtID" require="">


                                <label for="">Nome:</label>
                                <input type="text" class="form-control" name="txtNome" required value="<?php echo $txtNome; ?>" placeholder="" id="txtNome" require="">
                                <br>

                                <label for="">Descricao:</label>
                                <input type="text" class="form-control" name="txtDescricao" required value="<?php echo $txtDescricao; ?>" placeholder="" id="txtDescricao" require="">
                                <br>

                                <label for="">Foto:</label>
                                <input type="file" class="form-control" accept="image/*" name="txtFoto" value="<?php echo $txtFoto; ?>" placeholder="" id="txtFoto" require="">
                                <br>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button value="btnAdicionar" <?php echo $actionAdd; ?> class="btn btn-outline-success" type="submit" name="action">Adicionar</button>
                            <button value="btnModificar" <?php echo $actionMod; ?> class="btn btn-outline-warning" type="submit" name="action">Modificar</button>
                            <button value="btnEliminar" <?php echo $actionDel; ?> class="btn btn-outline-danger" type="submit" name="action">Eliminar</button>
                            <button value="btnCancelar" <?php echo $actionCan; ?> class="btn btn-outline-info" type="submit" name="action">Cancelar</button>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                + ADICIONAR PRODUTO +
            </button>

        </form>

        <div class="row">

            <table class="table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <?php foreach ($listaProdutos as $produtos) { ?>
                    <tr>
                        <td><img class="img-thumbnail" width="100px" src="../Imagens/<?php echo $produtos['Foto']; ?>" /></td>
                        <td><?php echo $produtos['Nome']; ?></td>
                        <td><?php echo $produtos['Descricao']; ?></td>
                        <td>

                            <form action="" method="post">

                                <input type="hidden" name="txtID" value="<?php echo $produtos['ID']; ?>">
                                <input type="hidden" name="txtNome" value="<?php echo $produtos['Nome']; ?>">
                                <input type="hidden" name="txtDescricao" value="<?php echo $produtos['Descricao']; ?>">
                                <input type="hidden" name="txtFoto" value="<?php echo $produtos['Foto']; ?>">

                                <input type="submit" class="btn btn-info" value="Selecionar" name="action">
                                <button value="btnEliminar" class="btn btn-danger" type="submit" name="action">Eliminar</button>

                            </form>


                        </td>

                    </tr>

                <?php } ?>


            </table>

        </div>

        <?php if ($mostrarModal) { ?>
            <script>
                $('#exampleModal').modal('show');
            </script>
        <?php } ?>

    </div>

</body>

</html>