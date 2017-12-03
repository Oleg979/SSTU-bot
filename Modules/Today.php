<?php
function today_message($userId) {
    

$html = new simple_html_dom();

//
$ini = new iniFile('users.ini');
$file = $ini->read();
$addr = $file[$userId]['group'];
if ($addr == null) return 'Вы не зарегистрированы. Пожалуйста, укажите свою группу с помощью команды "рег группа". Например: "рег б1-ифст12" или "рег с-нтс11"';
//echo $addr;
$html->load_file($addr);
//

$days = $html->find('div[class=today]');
if (count($days)==0) return 'Воскресенье, какие могут быть пары? &#128564;';
//echo 'Дней найдено: '.(count($days)-1).'<br>';

$offset = 0;
for($i=1;$i<count($days);$i++) {
    if(($days[$i]->class)=='rasp-table-row  empty today') $offset++;
    else break;
}

if ($offset >=4) return 'Сегодня у вас нет пар, отдыхайте &#128519;';
//echo 'Смещение равно: '.$offset.'<br>';



//// Номер пары
//$pieces = explode(" <br>",$days[2]->children(0)->innertext);
//$num =  $pieces[0];

// Предмет, тип и кабинет
//$subject = $days[2]->children(1)->children(0)->children(1)->innertext;
//$cab = $days[2]->children(1)->children(0)->children(0)->innertext;
//$type =  $days[2]->children(1)->children(0)->children(2)->innertext;
//echo $num.'. '.$subject.$type.' в '.$cab.' кабинете';

// Предмет, тип и кабинет для разделенной пары
//$subjecandtype = $days[3]->children(1)->children(0)->children(0)->innertext;
//$cab1 = $days[3]->children(1)->children(0)->children(1)->children(0)->innertext;
//$cab2 = $days[3]->children(1)->children(0)->children(2)->children(0)->innertext;
//echo $num.'. '.$subjecandtype.' - '.$cab1.', '.$cab2;

$empties=0; // Сколько пустых пар
for($i=1;$i<count($days);$i++)  {
    if(($days[$i]->class)=='rasp-table-row  empty today') $empties++;
}

$begin=1+$offset; // С какой пары начинаются занятия
if((count($days)-1-$empties)==1) $end = 'а';
else if ((count($days)-1-$empties)> 4) $end = '';
else $end = 'ы';

$msg = 'Сегодня у вас '.(count($days)-1-$empties). ' пар'.$end.', начиная с '.$begin.' пары: <br>'; // Заготовка сообщения


// Прописываем пустые пары
for($j=0;$j<$offset;$j++) {
    $msg = $msg.($j+1).". - <br>";
}

// Прописываем заполненные пары
for($i=1+$offset;$i<count($days);$i++) {
    
    $pieces = explode(" <br>",$days[$i]->children(0)->innertext);
    $num =  $pieces[0];
    $num = str_replace(" ","",$num);
    
    if(count($days[$i]->children(1)->children(0)->children)==4) {
        
        $subject = $days[$i]->children(1)->children(0)->children(1)->innertext;
        $cab = $days[$i]->children(1)->children(0)->children(0)->innertext;
        $type =  $days[$i]->children(1)->children(0)->children(2)->innertext;
        $msg = $msg.$num.'. '.$subject.' '.$type.' в '.$cab.' кабинете<br>';
    }
    
    else if(count($days[$i]->children(1)->children(0)->children)==3) {
     
        $subjecandtype = $days[$i]->children(1)->children(0)->children(0)->plaintext;
        $cab1 = $days[$i]->children(1)->children(0)->children(1)->children(0)->plaintext;
        $cab2 = $days[$i]->children(1)->children(0)->children(2)->children(0)->plaintext;
        $msg = $msg.$num.'. '.$subjecandtype.' - '.$cab1.', '.$cab2.'<br>';
    }
    
    else {
         $msg = $msg.$num.". -<br>";
    }
}

return $msg;
}

//echo today_message(208990427);

?>
