<?php


require_once("db_connection.php");

session_start();

// set the current user if exists
$currentuser = null;
if (isset($_SESSION["currentuser"])) {
    $currentuser = $_SESSION["currentuser"];
}

// load posts
try {

    $stmt = $db->prepare("SELECT * FROM gastos where usuario=?");
    $stmt->execute(array($currentuser));

    $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    die("exception! " . $ex->getMessage());
}

?>



<html>

<body>
    <?php include("header.php"); ?>
    <h1>Gastos</h1>

    <table border="1">
        <tr>
            <th>Descripcion</th>
            <th>Cantidad</th>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Fichero</th>
        </tr>

        <?php foreach ($gastos as $gasto) : ?>
            <tr>
                <td>
                    <?php echo $gasto["descripcion"] ?>
                </td>
                <td>
                    <?= $gasto["cantidad"] ?>
                </td>
                <td>
                    <?php echo $gasto["fecha"] ?>
                </td>
                <td>
                    <?= $gasto["tipo"] ?>
                </td>
                <td>
                    <?php echo $gasto["fichero"] ?>
                </td>

                <td>&nbsp;
                    <?php
                    //show actions ONLY for the author of the post (if logged)


                    if (isset($currentuser) && $currentuser == $gasto["usuario"]) : ?>

                        <?php
                        // 'Delete Button': show it as a link, but do POST in order to preserve
                        // the good semantic of HTTP
                        ?>
                        <form method="POST" action="delete_post.php" id="delete_post_<?= $gasto["id"]; ?>" style="display: inline">

                            <input type="hidden" name="id" value="<?= $gasto["id"] ?>">

                            <a href="#" onclick="
		      if (confirm('are you sure?')) {
			    document.getElementById('<?= $gasto["id"] ?>').submit() 
		      }">
                                Delete</a>

                        </form>

                        &nbsp;delete_post_

                        <?php
                        // 'Edit Button'
                        ?>
                        <a href="edit_post.php?id=<?= $gasto["id"] ?>">Edit</a>

                    <?php endif; ?>

                </td>
            </tr>
        <?php endforeach; ?>

    </table>

    <?php if (isset($currentuser)) : ?>
        <a href="add_post.php">Create post</a>
    <?php endif; ?>
</body>

</html>