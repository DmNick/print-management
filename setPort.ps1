param(
    [string]$Serwer,
    [string]$Name,
    [string]$Port
)
Import-Module PrintManagement
try  {
    #"$Serwer $Name $Port"
    $GetPrinterPort = Get-Printer -ComputerName "$Serwer" | Where-Object PortName -eq "$Port" -ErrorAction Ignore
    #$countPrinter = Get-Printer -ComputerName "$Serwer" -Name "$Name" | Where-Object PortName -eq "$port" | Select-Object -Property Name -ExpandProperty Name | Measure-Object | Select-Object -Property Count
    $countPrinterPort = $GetPrinterPort | Measure-Object | Select-Object -ExpandProperty Count
    #$countPrinter = Get-Printer -ComputerName "$Serwer" -Name "$Name" | where PortName -eq "$port" | Select-Object -Property Name -ExpandProperty Name | Measure-Object | Select-Object -Property Count
    if($countPrinterPort -gt 0){
        foreach ($PrinterPort in $GetPrinterPort) {
            "<div>$($PrinterPort.Name)</div>"
            try {
                Set-Printer -ComputerName "$Serwer" -Name "$($PrinterPort.Name)" -PortName "NotUsed:"
            }
            catch {
                Return "Oops, error przy zmianie portu drukarki $($PrinterPort.Name) na NotUser <br>$($_.Exception.Message)<br>"
            }
        }
    }
    if($countPrinterPort -eq 0){
        try {
            if(!(Get-PrinterPort -ComputerName "$Serwer" -Name "$Port" -ErrorAction Ignore)){
                Add-PrinterPort -ComputerName "$Serwer" -Name "$Port" -PrinterHostAddress "$Port"
            }
        }
        catch {
            Return "Oops, error przy tworzeniu portu $Port na $Serwer <br>$($_.Exception.Message)<br>"
        }
        
    }
    #Get-PrinterPort -ComputerName "$Serwer" -Name "$Port" -ErrorAction Ignore | Measure-Object | Select-Object -ExpandProperty Count
    Set-Printer -ComputerName "$Serwer" -Name "$Name" -PortName "$Port"
    
}
catch {
    #Something went wrong
    Return "Oops: Nie znaleziono urządzenia, bądź wprowadzono błędne dane.<br />$($_.Exception.Message)<br />"
}