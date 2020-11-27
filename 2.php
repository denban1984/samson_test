<?php

function convertString($InputString, $SubString){
   /* по условию задани€ инвертируетс€ только второе вхождение подстроки
    т.е. первое третье и остальные вхождени€ (если они есть) отаютс€ неизменными. “акое количесвто дополнительных переменных введено дл€ нагл€дности. —аможе решение может быть записано в одну строку
    но она будет трудна дл€ понимани€*/

 if(substr_count($InputString, $SubString)>1){
   //если вхождений больше одного преобразуем строку

   $ReversSubString = strrev($SubString); // инвертируем подстроку

   $SubStringLength = strlen($ReversSubString);// определ€ем длину подстроки пригодитс€ в substr_replace()

   $PositionFirstSubString = strpos($InputString,$SubString);// первое вхождение подстроки

   $PositionSecondSubString = strpos($InputString,$SubString,$PositionFirstSubString+1);// второе вхождение подстроки

   return substr_replace($InputString,$ReversSubString,$PositionSecondSubString,$SubStringLength);//замена подстроки по условию
  }
 else{
   //если вхожденее одно или его нет вовсе строка возвращаетс€ в неизменном виде
    return $InputString;
  }
}

?>