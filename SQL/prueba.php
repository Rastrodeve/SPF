<?php
echo "PASSWORD CIFRADA";
echo "<br>";
$hash=password_hash("1234", PASSWORD_DEFAULT);
echo "HASH: " . $hash;
//normal // $2y$10$ENrVzKzh.ATGNk0kOCyy5OBhYPzpbwVwVPVDyuRGeEEaidFCBG9.i
//glp // $2y$10$py6Lb931GbFUHtYuKMmCA.5EIEPLH.9P90KGC5kt8B7xhxXuxkzZi
?>