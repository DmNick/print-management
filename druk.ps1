param(
    [string]$UserAccount,
    [string]$SerwerWydruku
)
Import-Module ActiveDirectory
try  {
#$UserAccount = $env:USERNAME
  $userInfo = Get-ADUser -Filter "Name -like '*$UserAccount*'" 
  #$userInfo | ConvertTo-HTML
  
$($userInfo.GivenName) -replace 'DIT','IT' -replace 'ZIT','IT'
  Write-Host "<table class='table table-bordered table-hover text-nowrap'><tr style='text-align: left'><th>Nazwa</th><th>MAC</th><th>Adres IP</th><th>Serwer Wydruku</th><th></th></tr>"
  
        foreach ($drukMac in $userInfo){
          $Nazwa = $($drukMac.GivenName) -replace 'DIT','IT' -replace 'ZIT','IT'
          
          Write-Host "<tr><td>$($drukMac.Name)</td><td class='mac'>$($drukMac.samAccountName.ToUpper() -replace '..(?!$)', '$&:')</td><td class='ip'><img src='/ad/loading.gif' alt='wczytwanie..' width='20px'></td>
          <td class='sw' value='$Nazwa'><img src='/ad/loading.gif' alt='wczytwanie..' width='20px'></td><td class='sync'></td></tr>"
        }

  Write-Host "</table>"
  #"<div>$userinfoHTML</div>"
  #write-host $userInfo.SamAccountName
}
catch  {
  #Something went wrong
  Return "Oops: Nie znaleziono urządzenia, bądź wprowadzono błędne dane.<br />$($_.Exception.Message)<br />"
}