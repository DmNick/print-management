<?php
$serwer = $_GET['serwer']??null;
$name = $_GET['name']??null;
$port = $_GET['port']??null;
if($serwer && $name && $port){
    //echo "Set-Printer -ComputerName '$serwer' -Name '$name' -PortName '$port'<br>";
    $command = "Set-Printer -ComputerName '$serwer' -Name '$name' -PortName '$port'";
    //echo shell_exec('powershell -ExecutionPolicy Unrestricted -Command $env:USERNAME');
    //echo shell_exec('powershell -ExecutionPolicy Unrestricted -Command '.$command.'');
    echo shell_exec("powershell.exe -executionpolicy bypass -NoProfile -File setPort.ps1 -Serwer $serwer -Name $name -Port $port");
}
?>