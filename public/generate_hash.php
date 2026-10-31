<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Copy this hash: <br><br><strong>" . $hash . "</strong>";
?>