<?php
[
    "hostname" => $hostname,
    "username" => $username,
    "password" => $password,
    "database" => $database
] = parse_ini_file(__DIR__ . "/../config.ini", true)["mysql"];
