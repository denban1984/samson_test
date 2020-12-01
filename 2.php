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

    if(!array_key_exists($SortKey, $Chunk)){ throw new InvalidArgumentException('Îøèáêà! Âî âõîäíîì ìàññèâå ïî èíäåêñó '.$indx.' îòñóòñòâóåò êëþ÷ '.$SortKey);} 
    $indx++;
    }

    /*Сортировка функцией usort() с использованием собственной анонимной функции сравнения MyCompare($SortKey), анонимную функцию использовал для того чтобы можно было передавать любой ключ для сравнения а не жестко определенный внутри кода*/
    usort($InputArray,MyCompare($SortKey));
    return $InputArray;
}


define('HOST', 'localhost');   
define('USER', 'samson');      
define('PASSWORD', 'qwerty1'); 
define('DATABASE', 'test_samson'); 


function  importXml($XmlFile){
 $ErrorArray = array();  
 $ErrorArray[]=$XmlFile;
 $connect = mysqli_connect(HOST, USER, PASSWORD, DATABASE); 
    if (!$connect) {
    echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
    exit;
    }else{

 if(file_exists($XmlFile)){
  try{
    $xml = simplexml_load_file($XmlFile);
  }catch(Exception $e){$ErrorArray[] = 'Line 22 ERROR open XML Document '.$XmlFile.' ERROR MSG: '. $e->getMessage();}
 
 $ProductCode=0;
 $ProductName='';
 foreach( $xml->Товар as $productset){
  foreach($productset->attributes() as $key => $value) {//товар атрибуты
    if(strcasecmp($key,"Код")==0){$ProductCode=$value;}
    if(strcasecmp($key,"Название")==0){$ProductName=$value;}
    if($ProductCode>0 && $ProductName!=''){
      try{//записываем таблицу a_product
      $sql = mysqli_prepare($connect, 'INSERT INTO a_product (PRODUCT_CODE, PRODUCT_NAME) VALUES(?,?) ON DUPLICATE KEY UPDATE PRODUCT_CODE=?, PRODUCT_NAME=?');    
      mysqli_stmt_bind_param($sql , "isis", $ProductCode,$ProductName,$ProductCode,$ProductName);
      mysqli_stmt_execute($sql);
      mysqli_stmt_close($sql);
      }catch(Exception $e){$ErrorArray[] = 'Line 36  ERROR MSG: '. $e->getMessage(); }
      }
 }
  //Считываем цены
 foreach ($productset->Цена as $price) {
 try{
 $sql = mysqli_prepare($connect, 'SELECT ID FROM a_price_type WHERE PRICE_TYPE=?');
    $PriceType=$price['Тип'];
    $IdPriceType=-1;
    mysqli_stmt_bind_param($sql , "s", $PriceType);
    mysqli_stmt_execute($sql);
    mysqli_stmt_bind_result($sql, $IdPriceType);
    mysqli_stmt_fetch($sql);
    mysqli_stmt_close($sql);
  }catch(Exception $e){$ErrorArray[] = 'Line 50  ERROR MSG: '. $e->getMessage(); }

    if($IdPriceType!=-1){
      $priceval = (double)$price;
      try{
      $sql = mysqli_prepare($connect, 'INSERT INTO a_price (PRODUCT_CODE, PRICE_TYPE_ID, PRICE,LAST_MODIFY) VALUES(?,?,?,now())');    
      mysqli_stmt_bind_param($sql , 'iid', $ProductCode, $IdPriceType, $priceval);
      mysqli_stmt_execute($sql);
      mysqli_stmt_close($sql);
    }catch(Exception $e){$ErrorArray[] = 'Line 59  ERROR MSG: '. $e->getMessage(); }
    }else{$ErrorArray[] = 'Line 60 IdPriceType==-1';}
 }
 
 //Считываем свойства
 foreach ($productset->Свойства->children() as $key => $value) {
   if($key!='' && $value!=''){
    try{
      $sql = mysqli_prepare($connect, 'INSERT INTO a_property (PRODUCT_CODE, PRODUCT_PROPERTY, PROPERTY_VALUE) VALUES(?,?,?) ON DUPLICATE KEY UPDATE PRODUCT_CODE=?, PRODUCT_PROPERTY=?, PROPERTY_VALUE=?');    
      mysqli_stmt_bind_param($sql , 'ississ', $ProductCode, $key, $value, $ProductCode, $key, $value);
      mysqli_stmt_execute($sql);
      mysqli_stmt_close($sql);
    }catch(Exception $e){$ErrorArray[] = 'Line 71  ERROR MSG: '. $e->getMessage(); }
   }
 }

 //Считываем разделы
 foreach ($productset->Разделы->children() as $key => $value) {
 if($value!=''){
  try{
  $sql = mysqli_prepare($connect, 'SELECT CODE_RUBRIC FROM a_category WHERE NAME_RUBRIC=?');
    $NameRubric=$value;
    $CodeRubric=-1;
    mysqli_stmt_bind_param($sql , "s", $NameRubric);
    mysqli_stmt_execute($sql);
    mysqli_stmt_bind_result($sql, $CodeRubric);
    mysqli_stmt_fetch($sql);
    mysqli_stmt_close($sql);
     }catch(Exception $e){$ErrorArray[] = 'Line 87  ERROR MSG: '. $e->getMessage(); }
    if($CodeRubric!=-1){
      try{
      $sql = mysqli_prepare($connect, 'INSERT INTO a_product_category (PRODUCT_CODE, CODE_RUBRIC) VALUES(?,?)');    
      mysqli_stmt_bind_param($sql , 'ii', $ProductCode, $CodeRubric);
      mysqli_stmt_execute($sql);
      mysqli_stmt_close($sql);
    }catch(Exception $e){$ErrorArray[] = 'Line 94  ERROR MSG: '. $e->getMessage(); }
    }else{$ErrorArray[] = 'Line 60 CodeRubric==-1';}
 }
 }
 $ProductCode=0;
 $ProductName='';

    }
    mysqli_close($connect);
 }else{$ErrorArray[] = 'Файл '.$XmlFile.' не найден!';}
 }
 return $ErrorArray;
 }



?>