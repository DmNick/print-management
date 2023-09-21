param(
    [string]$UserAccount,
    [string]$action
)
$PSDefaultParameterValues['*:Encoding'] = 'utf8'
Import-Module ActiveDirectory
try  {
#$UserAccount = $env:USERNAME
  $userInfo = Get-ADUser -Identity $UserAccount -Properties LockedOut -ErrorAction Stop
  if($userInfo.LockedOut){
    "zablokowany"
    "<a href='/ad?user=$UserAccount&action=unlock'>Odblokuj</a>"
  }
  else {
    "odblokowany"
    #"<a href='ad?user=$UserAccount&action=unlock'>Odblokuj</a>"
  }
  if($action -eq "unlock"){
    "Action: $action"
    Unlock-ADAccount -Identity $UserAccount
  }
}
catch  {
  #Something went wrong
  Return "Oops: Nie znaleziono użytkownika, bądź wprowadzono błędne dane.<br />$($_.Exception.Message)<br />"
}