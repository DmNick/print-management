
<?php

include_once('../lib/routeros_api.class.php');

$iphost = '';
$userhost = '';
$passwdhost = '';
$mac = $_GET['mac'];
$API = new RouterosAPI();
$API->debug = false;
if ($API->connect($iphost, $userhost, $passwdhost) && isset($mac) && $mac !== ''){
    $getlease = $API->comm("/ip/dhcp-server/lease/print", array(
		"?mac-address" => "$mac",
    "?status" => "bound"
	));
	$TotalReg = count($getlease);
if($TotalReg == '0'){
  echo json_encode(["brak"]);
        return;
}
$tab = [];
for ($i = 0; $i < $TotalReg; $i++) {
	$lease = $getlease[$i];
	$id = $lease['.id'];
	$server = $lease['server'];
  
	$addr = $lease['address'];
    if(isset($addr) && $addr !== null && $addr !== ''){
      $tab[] = "<input type='radio' name='adresIp' id='adresIp' value='$addr'><label for='adresIp'>$server -> $addr</label></input>";
      
    }
}
echo json_encode($tab);

  } else {
    echo json_encode(["brak"]);
  }
$API->disconnect();
?>
