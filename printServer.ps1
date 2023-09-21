param(
    [string]$IT
)
Import-Module ActiveDirectory

try {
    $SerweryWydruku = ("druk","druk_zebra","impuls-mws")
    foreach($SerwerWydruku in $SerweryWydruku){
        $druk = Get-Printer -ComputerName $SerwerWydruku -Name "*$($IT)*"
        $countDruk = $druk | Measure-Object
        if($countDruk.Count -gt 0){
            foreach($drukSW in $druk){
                $drukIP = Get-PrinterPort -ComputerName $drukSW.ComputerName -Name "$($drukSW.PortName)"
                "<input type='radio' name='adresSw' id='adresSw' value='$($drukIP.PrinterHostAddress)' data-druk='$($drukSW.ComputerName)' data-drukname='$($drukSW.Name)'><label for='adresSw'></label><br>"
            }
        }
    }
}
catch  {
  #Something went wrong
  Return "Oops: Nie znaleziono urządzenia, bądź wprowadzono błędne dane.<br />$($_.Exception.Message)<br />"
}