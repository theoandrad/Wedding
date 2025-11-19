<?php
require_once __DIR__ . '/../includes/auth.php';
encerrarSessao();
redirecionar('/admin/login.php');
