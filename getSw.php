<?php
set_time_limit(300);

$IT = $_GET['IT'];
$serwerWydruku = $_GET['serwerWydruku'];
$resp = shell_exec("powershell.exe -executionpolicy bypass -NoProfile -File printServer.ps1 -IT $IT -SerwerWydruku $serwerWydruku");
echo $resp;
?>