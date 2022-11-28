<?php
//file: logout.php

session_start();

session_destroy();

// redirect the user to his personal_area
// However, since the session was destroyed,
// the user will be asked for login again
header("Location: login.php");

die();
