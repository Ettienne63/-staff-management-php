<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    echo "<pre>";

    print_r($_POST);

    echo "</pre>";

}
require_once __DIR__ . '/app/views/auth/login.php';