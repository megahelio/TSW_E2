<div id="menu" style="background-color:grey">
    <a href="posts.php">Posts</a> | <a href="login.php">Login</a>
    <?php if (isset($_SESSION["currentuser"])) : ?>
        <div style="float:right">Hello <?= $_SESSION["currentuser"] ?> <a href="logout.php">(Logout)</a></div>
    <?php endif ?>
</div>