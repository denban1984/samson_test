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


$ErrorArray=array();


function ProductSearch($connect, $CodeRubricArray){
  /*получили список всех рубрк теперь нужен список всех товаров входящих в эти рубрики
  При этом учтено что один товар может входить в неограниченное количество рубрик
  Для этих целей пройдемся по таблице a_product_category */
 $ResultArray = array();
 foreach ($CodeRubricArray as $Code){
   try{
    
    $sql = mysqli_prepare($connect, 'SELECT PRODUCT_CODE FROM a_product_category WHERE CODE_RUBRIC=?');
    mysqli_stmt_bind_param($sql , "i", $Code);
    mysqli_stmt_execute($sql);
    $result = mysqli_stmt_get_result($sql);

      while ($ResultRows = mysqli_fetch_array($result, MYSQLI_NUM))
        {
            foreach ($ResultRows as $Row) { 
              
              $ResultArray[$Row][]= $Code; 
            }     
        }


      }catch(Exception $e){$ErrorArray[] = 'RecursivaSearch Line 19  ERROR MSG: '. $e->getMessage();}
    
     }

    /*На выходе получаем массив в котором ключ элемента это код товара, а значения каждого элемента - массив, содержащий коды рубрик в которых этот товар найден */
  return $ResultArray;
}


function RecursivSearch($connect, $CodeRubric){
  /*так как по заданию глубина вложения рубрикатора произвольная, то для полного перебора вложений рубрикатора используется рекурсия*/
    $ResultArray = array();
    $TmpResultArray = array();
    $ResultArray[]=$CodeRubric;
 try{

  /*в таблице a_category_chain ищем "дочерние" рубрики для "родительской" рубрики переданой через $CodeRubric */
  $sql = mysqli_prepare($connect, 'SELECT CODE_RUBRIC FROM a_category_chain WHERE CODE_PARENT_RUBRIC=?');
  mysqli_stmt_bind_param($sql , "i", $CodeRubric);
  mysqli_stmt_execute($sql);
  $result = mysqli_stmt_get_result($sql);

    while ($ResultRows = mysqli_fetch_array($result, MYSQLI_NUM))
        {
            foreach ($ResultRows as $Row) { $TmpResultArray[] = $Row; }     
        }

   if (count($TmpResultArray)>0){
     
     /*если "дочерние" рубрики найдены то начинается их рекурсивный обход*/
    foreach($TmpResultArray as $value)
        {
           /*рекурсивный вызов тут*/
          $ResultArray = array_merge($ResultArray,RecursivSearch($connect, $value));
        }
     }

  }catch(Exception $e){$ErrorArray[] = 'RecursivaSearch Line 19  ERROR MSG: '. $e->getMessage();}
    
    asort($ResultArray);
  return $ResultArray;
}


function SaveToXml($connect, $XmlFilePath, $ProductArray){

 if(dirname($XmlFilePath)!='.'){} 
  else{ 
    $XmlFilePath = __DIR__.'\\'.$XmlFilePath;}
 try{
 $File = fopen($XmlFilePath, "w");
 fclose($File);
 }catch(Exception $e){$ErrorArray[] = ' Line 83 ERROR MSG: '. $e->getMessage(); }
 try{
 $ExportFile = file_get_contents($XmlFilePath);
 $ExportFile .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>".PHP_EOL;
 $ExportFile .= PHP_EOL;
 $ExportFile .= "<Товары>".PHP_EOL;
 $ExportFile .= PHP_EOL;
 //основной перебор
 foreach ($ProductArray as $ProductCode => $RubricArray) {
 
 //получаем название товара 
 $sql = mysqli_prepare($connect, 'SELECT PRODUCT_NAME FROM a_product WHERE PRODUCT_CODE=?');
    mysqli_stmt_bind_param($sql , "i", $ProductCode);
    mysqli_stmt_execute($sql);
    mysqli_stmt_bind_result($sql, $result);
    mysqli_stmt_fetch($sql);
    mysqli_stmt_close($sql);
  $ExportFile .= "<Товар Код=\"".$ProductCode."\" Название=\"".$result."\">".PHP_EOL;
  $ExportFile .= PHP_EOL;

 //Получаем цены 
 $sql = mysqli_prepare($connect, "SELECT a_price_type.PRICE_TYPE, a_price.PRICE FROM a_price_type INNER JOIN a_price ON(a_price_type.ID=a_price.PRICE_TYPE_ID)  WHERE a_price.PRODUCT_CODE=?");
   
    mysqli_stmt_bind_param($sql , "i", $ProductCode);
    mysqli_stmt_execute($sql);
    $result = mysqli_stmt_get_result($sql);
  
      while ($ResultRows = mysqli_fetch_array($result, MYSQLI_NUM))
        {
            for($i=0; $i<=count($ResultRows)-1;$i++)  {
              $ExportFile .= "<Цена Тип=\"".$ResultRows[$i]."\">";
              $i++;
              $ExportFile .= $ResultRows[$i]."</Цена>".PHP_EOL;
              $ExportFile .= PHP_EOL;
            }
        }
    mysqli_stmt_close($sql);
 
 //Получаем свойства
  $ExportFile .= "<Свойства>".PHP_EOL;
  $ExportFile .= PHP_EOL;
    $sql = mysqli_prepare($connect, 'SELECT PRODUCT_PROPERTY, PROPERTY_VALUE  FROM a_property WHERE PRODUCT_CODE=?');
   
    mysqli_stmt_bind_param($sql , "i", $ProductCode);
    mysqli_stmt_execute($sql);
    $result = mysqli_stmt_get_result($sql);
  
      while ($ResultRows = mysqli_fetch_array($result, MYSQLI_NUM))
        {
            for($i=0; $i<=count($ResultRows)-1;$i++)  {
              $NameProperty = $ResultRows[$i];
              $ExportFile .= "<".$NameProperty.">";
              $i++;
              $ExportFile .= $ResultRows[$i]."</".$NameProperty.">".PHP_EOL;
              $ExportFile .= PHP_EOL;
            }
        }
    mysqli_stmt_close($sql);
  $ExportFile .= "</Свойства>".PHP_EOL;  
  $ExportFile .= PHP_EOL;

 //Получаем разделы
 $ExportFile .= "<Разделы>".PHP_EOL;
 $ExportFile .= PHP_EOL;
 $sql = mysqli_prepare($connect, "SELECT a_category.NAME_RUBRIC FROM a_category INNER JOIN a_product_category ON(a_category.CODE_RUBRIC=a_product_category.CODE_RUBRIC)  WHERE a_product_category.PRODUCT_CODE=?");
   
    mysqli_stmt_bind_param($sql , "i", $ProductCode);
    mysqli_stmt_execute($sql);
    $result = mysqli_stmt_get_result($sql);
  
      while ($ResultRows = mysqli_fetch_array($result, MYSQLI_NUM))
        {
            foreach ($ResultRows as $Row) { 
              
              $ExportFile .="<Раздел>".$Row."</Раздел>".PHP_EOL; 
              $ExportFile .= PHP_EOL;
            }   
        }
    mysqli_stmt_close($sql);

 $ExportFile .= "</Разделы>".PHP_EOL; 
 $ExportFile .= PHP_EOL; 
 $ExportFile .= "</Товар>".PHP_EOL;
 $ExportFile .= PHP_EOL;
 }
 $ExportFile .= "</Товары>".PHP_EOL;
 $ExportFile .= PHP_EOL;
 file_put_contents($XmlFilePath, $ExportFile);
 }catch(Exception $e){$ErrorArray[] = ' Line 98 ERROR MSG: '. $e->getMessage(); }
}


function  exportXml($XmlFilePath,$CodeRubric){
 $Result = FALSE; 
 $ErrorArray[]=$XmlFilePath;
 $connect = mysqli_connect(HOST, USER, PASSWORD, DATABASE); 
    if (!$connect) {
    echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
    exit;
    }else{

   /*Каскадный вызов  RecursivSearch -> ProductSearch -> SaveToXml
    В этих трех функциях реализована вся логика работы моей функции exportXml */
  SaveToXml($connect, $XmlFilePath , ProductSearch($connect, RecursivSearch($connect, $CodeRubric) ) );
 
  } 
}



?>