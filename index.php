<meta charset="utf-8" />
<link rel="stylesheet" type="text/css" href="/css/mikhmon-ui.dark.min.css"/>
<script src="/js/jquery.min.js"></script>
<a class="btn bg-success" href="./">Główna</a>
<a class="btn bg-primary" href="./?locked=1">Zablokowani</a>
<a class="btn bg-primary" href="/tab">Tablica</a>
<style>
    table.table tr:not(:first-child){
        height: 50px;
        min-height: 50px;
    }
    table.table .sync {
        width: 95px;
    }
</style>
<?PHP
setlocale(LC_CTYPE, "pl_PL.UTF-8");
set_time_limit(400);

$mac = isset($_GET['mac']) ? escapeshellarg($_GET['mac']) : '';
$user = isset($_GET['user']) ? escapeshellarg($_GET['user']) : '';
$druk = isset($_GET['druk']) ? escapeshellarg($_GET['druk']) : '';
$action = isset($_GET['action']) ? escapeshellarg($_GET['action']) : 'null';
$locked = isset($_GET['locked']) ? escapeshellarg($_GET['locked']) : '';
$serwerWydruku = isset($_GET['serwerWydruku']) ? escapeshellarg($_GET['serwerWydruku']) : 'null';
//echo escapeshellarg('test');
echo "<script>console.log(".json_encode($_SERVER).")</script>";
//echo $_SERVER['COMPUTERNAME'];
//str_replace("'", "\'", json_encode($array));

//MYSQL ---------------------------------------------------------
include_once('../log.php');
//END MYSQL -----------------------------------------------------

echo "
    <form method='get'>
    <input type='text' placeholder='login usera' name='user' value=$user>
    <input type='submit' value='Sprawdź' />
    <input type='reset' />
    </form>
    <form method='get'>
    <input type='text' placeholder='nr IT drukarki' name='druk' value=$druk>
    
    <input type='submit' value='Sprawdź' />
    <input type='reset' />
    </form>
    ";


$userName = shell_exec('powershell -ExecutionPolicy Unrestricted -Command $env:USERNAME');

//echo "<div>aktywny user: " . $userName . "</div>";

if(isset($user) && $user !== null && $user !== ''){
    echo "<br>user::: ".$user;
    echo shell_exec("powershell.exe -executionpolicy bypass -NoProfile -File user.ps1 -UserAccount $user -Action $action");
    echo "<br>action::: ".$action;
    if(isset($action) && $action == '"unlock"'){
        echo "<div>Odblokowano</div>";
        //shell_exec("powershell -ExecutionPolicy Unrestricted -Command 'Unlock-ADAccount -Identity $user'");
        //header("Location: http://it14679/");
    }
}
if(isset($druk) && $druk !== null && $druk !== ''){
    echo "druk::: ".$druk."<br>";
    //echo shell_exec("powershell.exe -executionpolicy bypass -NoProfile -File druk.ps1 -UserAccount $druk -SerwerWydruku $serwerWydruku");
    echo shell_exec("powershell.exe -executionpolicy bypass -NoProfile -File druk.ps1 -UserAccount $druk -SerwerWydruku $serwerWydruku");

    echo "<script>

    //----------------------------------------------------
    let firstController = new AbortController();let firstSignal = firstController.signal;
    let controller = new AbortController();let signal = controller.signal;
    document.querySelectorAll('table.table tr:not(:first-child)').forEach(async(el,index) => {
        el.querySelector('.mac').style.fontWeight = 'bold';
        await fetch('./getIp.php?mac='+el.querySelector('.mac').innerHTML, { firstSignal }).then(e2 => e2.json()).then(e2 => {el.querySelector('.ip').innerHTML = e2.join('<br>')}).then(async()=>{
            await fetch('./getSw.php?IT='+el.querySelector('.sw').getAttribute('value')+'&serwerWydruku=$serwerWydruku',{ signal }).then(e3 => e3.text()).then(e3 => {el.querySelector('.sw').innerHTML = e3;
                el.querySelectorAll('[name^=\"adres\"]').forEach((ee, eeindex) => {
                    let oldName = ee.getAttribute('name');
                    ee.setAttribute('name',oldName+index);
                    ee.setAttribute('id',oldName+'Label'+index+eeindex);
                    ee.nextElementSibling.setAttribute('for',oldName+'Label'+index+eeindex);
                    if(ee.getAttribute('name').startsWith('adresSw')){
                        ee.nextElementSibling.innerHTML = '\\\\\\\'+ee.getAttribute('data-druk')+'\\\'+ee.getAttribute('data-drukname')+' -> '+ee.getAttribute('value');
                    }
                    ee.addEventListener('change',()=>{
                        let adresIpCheck = el.querySelectorAll('[name=\"adresIp'+index+'\"]:checked').length;
                        let adresSwCheck = el.querySelectorAll('[name=\"adresSw'+index+'\"]:checked').length;
                        let adresIp = el.querySelector('[name=\"adresIp'+index+'\"]:checked');
                        let adresSw = el.querySelector('[name=\"adresSw'+index+'\"]:checked');
                        if(adresIpCheck && adresSwCheck){
                            let adresDruk = adresSw.getAttribute('data-druk');
                            let adresDrukName = adresSw.getAttribute('data-drukname');
                            el.querySelector('.sync').innerHTML = '';
                            console.log('stary adres: '+adresSw.value);
                            console.log('nowy adres: '+adresIp.value);
                            //el.querySelector('.sync').innerHTML = '<button class=\"btn btn-primary\">Zmień</button>';
                            let btt = document.createElement('button');
                            btt.classList = 'btn btn-primary';
                            btt.innerHTML = 'Zmień';
                            btt.addEventListener('click',async ()=>{
                                if(adresIp.value === adresSw.value){alert('To ten sam adres! Zmiana zaniechana');return false}
                                if(confirm('Czy chcesz zmienić port drukarki na serwerze wydruku na '+adresIp.value)){
                                    console.log('poszła zmiana z '+adresSw.value??null+' -> na '+adresIp.value); 
                                    console.log(el);
                                    el.style.filter = 'blur(3px)';
                                    await fetch('./setPort.php?serwer='+adresDruk+'&name='+adresDrukName+'&port='+adresIp.value).then(el2=>el2.text()).then((el2)=>{
                                        //alert(el2);
                                        
                                        console.log(el2);
                                        
                                        adresSw.setAttribute('value',adresIp.value);
                                        adresSw.nextElementSibling.innerHTML = '\\\\\\\'+adresSw.getAttribute('data-druk')+'\\\'+adresSw.getAttribute('data-drukname')+' -> '+adresIp.value;
                                        el.style.filter = '';
                                    });
                                };
                            });
                            btt.addEventListener('focus',()=>{
                                el.style.border = '2px solid red';
                            });
                            btt.addEventListener('focusout',()=>{
                                el.style.border = '';
                            });
                            el.querySelector('.sync').append(btt);
                        }
                    });
                });
                
            })
        });
    });
    //----------------------------------------------------
    
    $( window ).bind('beforeunload', function()
    {
        controller.abort();
        firstController.abort();
        document.querySelectorAll('.sw').forEach(el => el.innerHTML = '<b>X</b>');
        document.querySelectorAll('.ip').forEach(el => el.innerHTML = '<b>X</b>');
    });
    </script>";
}
if(isset($locked) && $locked !== null && $locked !== ''){
    $blockedUsers = shell_exec('powershell -ExecutionPolicy Unrestricted -Command "$le = Search-ADAccount -LockedOut;foreach($user in $le){$user.Name;$user.SamAccountName;Write-Output `<a href=\"/ad/?user=$($user.SamAccountName)\">Odblokuj</a>}"');
    $blockedUsersHTML = shell_exec('powershell -ExecutionPolicy Unrestricted -Command "Search-ADAccount -LockedOut | ConvertTo-HTML"');
    
    
    if($blockedUsers == ''){
        echo "Brak zablokowanych kont w AD";
    }
    else {
        echo "<div class='content'>Zablokowani userzy: " . $blockedUsers . "</div>";
    }
}   
?>
<div id="content"></div>
<div style="display:none">http://vcloud-lab.com/entries/powershell/executing-powershell-script-from-php-html-web-server</div>