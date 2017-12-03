<?php

function Game1($id,$Msg){

global $mode;

////////////////////////////////////////////////////////////////////////////////

$keywords = array(
'созд' => '1', 'подкл' => '2', 'выход' => '3', 'старт' => '4', 'рейт' => '7',
//англ
'a1' => '5', 'a2' => '5', 'a3' => '5',
'b1' => '5', 'b2' => '5', 'b3' => '5',
'c1' => '5', 'c2' => '5', 'c3' => '5',

//русские
'а1' => '6', 'а2' => '6', 'а3' => '6',
'б1' => '6', 'б2' => '6', 'б3' => '6',
'с1' => '6', 'с2' => '6', 'с3' => '6'
);

    foreach($keywords as $key => $value){
    $res = strripos($Msg, $key);

        if ($res !== false){
            $res = '1';
            break;
        }else{
            $res = '0';
        }

}

////////////////////////////////////////////////////////////////////////////////

if($res == '1') {
    global $ini;
    
        switch ($value) {
        
        case 1:
        $res = TestServer($id);
        $res = explode('|',$res);
        $Server = $res[1];
        TimeUpdate($Server);
        
        if($res[0] < 0){
            Sozdat($id);
            $ini->addParam($Server, "Player1", $id);
            $ini->addParam($Server, "Player2", '');
            GenrateServer($Server,$id);
            $message = "0|0|1|&#127381; Создан сервер №$Server.<br>Теперь попросите вашего друга подключиться к этому 
            серверу с помощью команды 'подкл $Server'.";
        }
        
        else $message = "0|0|1|Вы уже на сервере №$Server! Теперь позовите друга.";
 
        break;
        
////////////////////////////////////////////////////////////////////////////////
        
        case 2:
        $res = TestServer($id);
        $res = explode('|',$res);
        $Server = $res[1];
        
        if($res[0] < 0){
                    $ServerMsg = explode(" ",$Msg);
                    if($ServerMsg[1] != NULL){
                        TimeUpdate($ServerMsg[1]);
                        $res = TestConnect($ServerMsg[1],$id);
                        $res = explode('|',$res);
                        if($res[0] > 0){
                            $TestAllPiople = TestAllPiople($ServerMsg[1]);
                            $TestAllPiople = explode('|',$TestAllPiople);
                            $Player1 = $TestAllPiople[1];
                            $Player2 = $TestAllPiople[2];
                            $message = $Player1.'|'.$Player2.'|1|'.$res[1];
                        }
                        else
                        {
                            $message = '0|0|1|'.$res[1];
                        }
                    }
                    else
                    {
                        $message = "0|0|1|Вы не указали сервер поключения!";
                    }
        }
        
        else
        {
            $message = "0|0|1|Вы уже на сервере $Server! Для выхода из сервера введите 'выход'.";
        }
        
        break;
        
////////////////////////////////////////////////////////////////////////////////

        case 3:
  
        $res = TestServer($id);
        $res = explode('|',$res);
        
        if($res[0] > 0) {
            $Server = $res[1];
            $TestAllPiople = TestAllPiople($Server);
            $TestAllPiople = explode('|',$TestAllPiople);
             $userInfo2 = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$id}&v=5.0&lang=ru")); 
             $Name2 = $userInfo2->response[0]->first_name;
            if($TestAllPiople[0] > 0) {
                $Player1 = $TestAllPiople[1];
                $Player2 = $TestAllPiople[2];
                $message = "$Player1|$Player2|1|&#127379; $Name2 выходит с сервера №$Server, теперь сервер удалён.";
            }
            else
            {
                $message = "0|0|1|&#127379; $Name2 выходит с сервера №$Server, теперь сервер удалён.";
            }
            $ini->deleteSection($Server);
            $Max = $ini->read()['Stop']['Max'];
            $MaxNow=$Max-1;
            $ini->addParam('Stop', "Max", $MaxNow);
        }
        else
        {
            $message = "0|0|1|Сначала подключитесь или создайте сервер!";
        }
        break;
        
 ////////////////////////////////////////////////////////////////////////////////
       
        case 4:
        $res = TestServer($id);
        $res = explode('|',$res);
        $Server = $res[1];
        if($res[0] > 0){
            $TimeTest = TimeEnd($Server);
            if($TimeTest > 0){
                $res = TestAllPiople($Server);
                if($res[0] > 0){
                        $res = Start($Server,$id);
                        if($res > 0){
                                $TestAllPiople = TestAllPiople($Server);
                                $TestAllPiople = explode('|',$TestAllPiople);
                                $Player1 = $TestAllPiople[1];
                                $Player2 = $TestAllPiople[2];
                                
                            $res = WhoHOD($Server);
                            $res = explode('|',$res);
                            $ID = $res[0];
                            $Znak = $res[1];
                            if ($Znak=='X' or $Znak == 'Х') $Znak = 'крестиками.';
                            else $Znak = 'ноликами.';
                            
                            $res = Tabl($Server);
                            $userInfo = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$ID}&v=5.0&lang=ru")); 
                            $Name = $userInfo->response[0]->first_name; 
                            $message = "$Player1|$Player2|$res|".'<br>'."Первый ход делает $Name, ходит $Znak";
                        }else{
                            $message = "0|0|1|Вы не создатель сервера!";
                        }
                }else{
                    $message = "0|0|1|Вы один на сервере, позовите друга!";
                }
            }else{
                $message = "0|0|1|Сервер был удален, так как вы слишком долго были неактивны!";
            }
        }else{
            $message = "0|0|1|Сначала подключитесь или создайте сервер!";
        }
        break;
        
 ////////////////////////////////////////////////////////////////////////////////
      
        case 5:
        $res = TestServer($id);
        $res = explode('|',$res);
        $Server = $res[1];
        if($res[0] > 0){
            TimeUpdate($Server);
            $res = TestWin($Server);
            $res = explode('|',$res);
            $Znak = $res[1];
            $TimeTest = TimeEnd($Server);
            
            if($TimeTest > 0){
                       // if($res[0] == '0'){
                        $end = $res[0];
                            $res = PervHOD($Server);
                            $res = explode('|',$res);
                            
                            if($res[0] > 0){
                                    $res = Play($Server,$id,$Msg);
                                    $res = explode('|',$res);
                                    $end = $res[0];
                                    
                                    if($res[0] > 0){
                                        $TestAllPiople = TestAllPiople($Server);
                                        $TestAllPiople = explode('|',$TestAllPiople);
                                        $Player1 = $TestAllPiople[1];
                                        $Player2 = $TestAllPiople[2];
                                        $playMsg = $res[1];
                                        $res = Tabl($Server);
                                        $message = "$Player1|$Player2|$res|".'<br>'.$playMsg;
                                        
                                        if($end == 3){
                                            $ini->deleteSection($Server);
                                            
                                            $Max = $ini->read()['Stop']['Max'];
                                            $MaxNow=$Max-1;
                                            $ini->addParam('Stop', "Max", $MaxNow);
                                            
                                        }
                                    }else{
                                        $message = "0|0|1|".$res[1];
                                    }
                            }else{
                                $message = "0|0|1|".$res[1];
                            }
                        /*}else{
                            
                                if($end > 0 ){
                                    $message = "$Player1|$Player2|1|Победил игрок играющий $Znak";
                                }else{
                                    $message = "$Player1|$Player2|1|Ничья!";
                                }
                                
                                $ini->deleteSection($Server); 
                            }*/
                                
                        
            }else{
                $message = "0|0|1|&#127379; Сервер был удален, так как вы слишком долго были АФК!";
            }

        }else{
            $message = "0|0|1|Сначала подключитесь или создайте сервер!";
        }
        break;
////////////////////////////////////////////////////////////////////////////////

        
        case 6:
            $message = "0|0|1|Пожалуйста, используйте латиницу: a1 b1 c1 и так далее!";
        break;
////////////////////////////////////////////////////////////////////////////////
     
        case 7:
        $reit = rate_message();
        $message = "0|0|1|$reit";
        break;
        
        
        }
    }
////////////////////////////////////////////////////////////////////////////////
    
    else {
        if($Msg == '!выйти!') return '0|0|1|Вы вышли из режима игры.';
        return '0|0|1|Для выхода из режима игры введите /выйти (не забудьте /)';
    }
    
$ini->save();
return $message;
}
?>
