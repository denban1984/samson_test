<?php

function convertString($InputString, $SubString){
   /* по условию задания инвертируется только второе вхождение подстроки
    т.е. первое третье и остальные вхождения (если они есть) отаются неизменными. Такое количесвто дополнительных переменных введено для наглядности. Саможе решение может быть записано в одну строку
    но она будет трудна для понимания*/

 if(substr_count($InputString, $SubString)>1){
   //если вхождений больше одного преобразуем строку

   $ReversSubString = strrev($SubString); // инвертируем подстроку

   $SubStringLength = strlen($ReversSubString);// определяем длину подстроки пригодится в substr_replace()

   $PositionFirstSubString = strpos($InputString,$SubString);// первое вхождение подстроки

   $PositionSecondSubString = strpos($InputString,$SubString,$PositionFirstSubString+1);// второе вхождение подстроки

   return substr_replace($InputString,$ReversSubString,$PositionSecondSubString,$SubStringLength);//замена подстроки по условию
  }
 else{
   //если вхожденее одно или его нет вовсе строка возвращается в неизменном виде
    return $InputString;
  }
}


function MyCompare($SortKey) {
  //анонимная функция используется для сортировки массива в mySortForKey($InputArray, $SortKey)
    return function ($FirstElement, $SecondElement) use ($SortKey) {
         if ($FirstElement[$SortKey] == $SecondElement[$SortKey]) {
           return 0;
          }
         return ($FirstElement[$SortKey] < $SecondElement[$SortKey]) ? -1 : 1;
    };
}


function mySortForKey($InputArray, $SortKey){
   
   $indx=0;
   
   foreach($InputArray as $Chunk){
     /*проверяю наличие ключа $SortKey во всех элементах массива. Вообще можно было бы проверять наличие ключа в анонимной функции MyCompare($SortKey) и избавиться от этого цикла если бы не требование указать индекс элемента входного массива в котором отсутвует нужный ключ */

    if(!array_key_exists($SortKey, $Chunk)){ throw new InvalidArgumentException('Ошибка! Во входном массиве по индексу '.$indx.' отсутствует ключ '.$SortKey);} 
    $indx++;
    }

    /*Сортировка функцией usort() с использованием собственной анонимной функции сравнения MyCompare($SortKey), анонимную функцию использовал для того чтобы можно было передавать любой ключ для сравнения а не жестко определенный внутри кода*/
    usort($InputArray,MyCompare($SortKey));
    return $InputArray;
}

?>