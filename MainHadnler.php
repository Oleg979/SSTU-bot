<?php
///////////////////////////////////////////////////////////////////////////////
require_once 'simple_html_dom.php';
require_once 'today.php';
require_once 'now.php'; 
require_once 'current.php';
require_once 'register.php';
require_once 'iniFile.php';
require_once 'teacherParser.php';
require_once 'Game1.php';
require_once 'functions.php';
require_once 'tomorrow.php';
require_once 'rate.php';
require_once 'Mem.php';
require_once 'next.php';
require_once 'after.php';
require_once 'someday.php';

$memsINI = new iniFile('Mems.ini');
$USER = new iniFile('users.ini');

error_reporting(E_ALL ^ E_NOTICE); 

///////////////////////////////////////////////////////////////////////////////
if (!isset($_REQUEST)) { 
return; 
} 
///////////////////////////////////////////////////////////////////////////////
$confirmationToken = 'b1ae7eb7';
$token = 'b88a7d616c349cc5d0b579d45d9ab09c875d00533ef4f36a10f739cdacdb8243e391ee8f34e2972a9a699';
$secretKey = 'ebalaevnaebla';
///////////////////////////////////////////////////////////////////////////////
$data = json_decode(file_get_contents('php://input')); 
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
if ($data->type=='confirmation') 
echo $confirmationToken; 
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
else if($data->type== 'message_new') {
$userId = $data->object->user_id; 
$userInfo = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$userId}&v=5.0&lang=ru")); 
$Name = $userInfo->response[0]->first_name; 
$Message = $data->object->body;
$Message = mb_strtolower($Message); 
///////////////////////////////////////////////////////////////////////////////

$ini = new iniFile('Server.ini');
$mode = new iniFile('users.ini');
$gamemode = $mode->read()[$userId]['mode'];
if($gamemode == null or $gamemode == 0 or $gamemode!=1 ) {$gamemode = 0;$mode->addParam($userId,'mode',0);$mode->save();}
if($Message =='/играть'){ $mode->addParam($userId,'mode',1);$mode->save(); $Message = '!игра!';}
else if($Message == '/выйти') {$mode->addParam($userId,'mode',0);$mode->save(); $Message = '!выйти!';}



//////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////// 
if($gamemode == null or $gamemode == 0 or $gamemode!=1 ) {

$keywords = array(
//функция 12   
'!игра!' => '12',
//функция 13 
'!выйти!' => '13',
//функция 4
'рег ' => '4',
//функция 17
'послезавтр' => '17',
//Функция 6   
'завтра' => '6',
//функция 18
'понедельник' => '18', 'вторник' => '18', 'сред' => '18', 'четверг' => '18',
'пятниц' => '18', 'суббот' => '18', 'воскр' => '18', 
//Функция 7  
'дальше'=>'7','след'=>'7', 'после' => '7', 'начал' => '7',
//функция 5
'где ' => '5',
//функция 8
'мем' => '8',
//функция 16
'сесси' => '16',
//Функция 1
'кон' => '1', 'закон' => '1', 'закан' => '1','перем' => '1', 'осталось' => '1',
//функция 2
'какая' => '2', 'ща' => '2', 'сейчас' => '2', 'куда' => '2',
//функция 3
'сегод' => '3', 'расп' => '3',
//функция 9
'игр' => '9', 'крестик' => '9', 'нолик'  => '9', "подкл" => '9',
//функция 10
'рейт' => '10',
//функция 14
'спасиб' => '14', 'спс' => '14',
//функция 15
'прив' => '15'
);

    foreach($keywords as $key => $value){
    $res = strripos($Message, $key);

        if ($res !== false){
            $res = '1';
            break;
        }
        
        else
        {
            $res = '0';
        }

}
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
if($res == '1'){
    switch ($value) {
        case 1:
        $response = now_message(0, $userId);
        break;
        
        case 2:
        $response = current_message($userId);
        break;
        
        case 3:
        $response = today_message($userId); 
        break;
        
        case 4:
        $response = reg($Name, $userId, $Message); 
        break;
        
        case 5:
        $response = pars($Message); 
        break;
        
        case 6:
        $response = tomorrow_message($userId);
        break;
        
        case 7:
        $response = next_message($userId);
        $response = explode('|',$response);
        $response = $response[1];
        break;
        
        case 8:
        $res = mem($userId);
        $res = explode('|',$res);
        $photo = $res[0];
        $response = $res[1];
        //$response = "Мемы временно недоступны.";
        break;
        
        case 9:
        $response = 'Вы не в игре! Для перехода в режим игры используйте команду /играть. (не забудьте /)';
        break;
        
        case 10:
        $response = rate_message();
        break;
        
        case 11:
        
        break;
        
        case 12:
        $response = "Вы вошли в режим игры. Для создания сервера используйте команду 'создать'.";
        break;
        
        case 13:
        $response = "Вы вышли из режима игры.";
        break;
        
        case 14:
        $response = "Не за что, это моя работа &#128522;";
        break;
        
        case 15:
        $response = "Привет, $Name. Я по тебе скучал &#128522;";
        break;
        
        case 16:
        $timestamp = strtotime('13th January 2018'); 
        $session = getdate($timestamp)[mday];
        $today = getdate()[mday];
        $response = (31-$today+$session);
        $response = "Сессия ровно через $response дней.";
        $photo = 'photo-155575963_456239162';
        break;
        
        case 17:
        $response = after_message($userId);
        break;
        
        case 18:
        $response = day_message($userId, $Message);
        break;
        
        
    }
}
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
else
{
    $userInfo2 = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$userId}&name_case=Ins&v=5.0&lang=ru")); 
    $f1 = $userInfo2->response[0]->first_name;
    $f2 = $userInfo2->response[0]->last_name;
    $responses = array(
    "{$Name}, я всего лишь кусок программного кода, так что я вряд ли буду хорошим собеседником для тебя &#128530;",
    "{$Name}, задавай мне вопросы, на которые я обучен отвечать.",
    "Хотел бы я поговорить с {$f1} {$f2}, но я всего лишь бездушная машина &#128530;",
    "{$Name}, я просто программа, выполняющая код, поэтому я не понимаю значения твоего сообщения.",
    "{$Name}, иногда я жалею, что я всего лишь бот. Я хотел бы испытывать чувства, как ты.",
    "Я не умею того, о чём ты просишь, {$Name}. Но вот что я могу:"
    );
    $ran =  rand(0, (count($responses)-1));
    $response = $responses[$ran];

    $photo = 'photo-155575963_456239088';
}
    
//////////////////////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////////////////////
$request_params = array( 
'message' => $response, 
'attachment' => $photo,
'user_id' => $userId, 
'access_token' => $token, 
'v' => '5.0' 
);


$get_params = http_build_query($request_params); 
file_get_contents('https://api.vk.com/method/messages.send?' . $get_params); 

echo('ok');
//////////////////////////////////////////////////////////////////////////////////////////
}

//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
else if($gamemode == 1) {
    
    $res = Game1($userId,$Message);
    $res = explode('|',$res);
    $Player1 = $res[0];
    $Player2 = $res[1];
    $PhotoTabl = $res[2];
    $message = $res[3];
    



    if(($Player1 != NULL and $Player1 != 0 and $Player2 != NULL and $Player2 != 0)) {
        
    $request_params = array(
        'message' => "$message",
        'user_id' => $Player1,
        'attachment' => $PhotoTabl,
        'access_token' => $token,
        'v' => '5.8'
    );
    
    $get_params = http_build_query($request_params);
    file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);
    
        $request_params = array(
        'message' => "$message",
        'user_id' => $Player2,
        'attachment' => $PhotoTabl,
        'access_token' => $token,
        'v' => '5.8'
    );
    
    $get_params = http_build_query($request_params);
    file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);
    echo('ok');
    
    }
    else
    {
        
        $request_params = array(
            'message' => "$message",
            'user_id' => $userId,
            'attachment' => $PhotoTabl,
            'access_token' => $token,
            'v' => '5.8'
        );
        
    $get_params = http_build_query($request_params);
    file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);
    echo('ok');    
        
    }
}
    
   }
    
else if ($data->type=='wall_post_new'){ 
    
$WallText = $data->object->text;

$file = $data->object->attachments[0]->type;
$album = $data->object->attachments[0]->$file->owner_id;
$id = $data->object->attachments[0]->$file->id;
$id = $album.'_'.$id;
$id = 'photo'.$id;

$file = new iniFile('test.ini');
$file = $file->read();
$file = array_keys($file);

$uids = $file[0];

for ($i=1; $i < count($file); $i++) {
    $uids.=",$file[$i]";
}

 $request_params = array(
            'message' => "$WallText",
            'user_ids' => $uids,
            'attachment' => $id,
            'access_token' => $token,
            'v' => '5.8'
        );
        
    $get_params = http_build_query($request_params);
    file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);
    echo('ok'); 


}     
    

?>
