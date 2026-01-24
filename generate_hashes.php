<?php

$users = [
    ['email' => 'admin@educationpriorite.local', 'password' => 'Admin@1234'],
    ['email' => 'redacteur@educationpriorite.local', 'password' => 'Redacteur@1234'],
];

foreach ($users as $user) {
    $hash = password_hash($user['password'], PASSWORD_BCRYPT);
    echo $user['email'] . ' => ' . $hash . PHP_EOL;
}
