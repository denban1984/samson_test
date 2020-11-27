<?php

function  findSimple ($BeginRange, $EndRange){
 //функция реализована на основе алгоритма "решето Эратосфена"
 
  $ResultArray = array();

   for($NextNumbwer = $BeginRange; $NextNumbwer <= $EndRange; ++$NextNumbwer){
    
     if($NextNumbwer == 2){ $ResultArray[] = $NextNumbwer; continue;} 
    
     elseif($NextNumbwer == 1 || ($NextNumbwer % 2) == 0){continue;}
    
     else{
        $Count = 1;
        for($i = 3; ($i*$i) <= $NextNumbwer; $i += 2 ){ // i*i для сужения диапазона поиска
        if($NextNumbwer % $i == 0){$Count++;}
        } 
        if($Count == 1){ $ResultArray[] = $NextNumbwer; }
      }
   }
  return $ResultArray;
}


function createTrapeze($InputArray){
  
  $ResultArray = array();
  
  $Keys = array('a', 'b', 'c');

  $ChunkSize = 3;

  foreach(array_chunk($InputArray, $ChunkSize) as $ArrayChunk){

    /*Проверка на случай если входной массив не кратен 3.
    Если входной массив не кратен 3 то от него останется "хвост" длина которого будет < 3 
    (это "остаток" от выполнени функции array_chunk($a, $ChunkSize) )
    Чтобы не потерять данные из этого "хвоста" надо уровнять длину массива $Keys 
    с длиной "хвоста" чтобы корректно отработала функция array_combine() */

    if(count($Keys) != count($ArrayChunk)){
       $Size = (count($Keys) > count($ArrayChunk)) ? count($ArrayChunk) : count($Keys);
       $TmpKeys=array_slice($Keys, 0, $Size);
       $TmpArrayChunk=array_slice($ArrayChunk, 0, $Size);
       $ResultArray[] = array_combine($TmpKeys,$TmpArrayChunk);
      }
    else{
       $ResultArray[] = array_combine($Keys,$ArrayChunk);
      }
    }
  return $ResultArray; 
}


function  squareTrapeze(&$InputArray){
 //функция работает с массивом, переданным по ссылке. То есть, как и сказано в условии, меняет исходный массив.
 
   foreach($InputArray as &$Chunk){
    // только при наличии всех данных можно производить расчет
    if(array_key_exists('a', $Chunk) 
       && array_key_exists('b', $Chunk) 
       && array_key_exists('c', $Chunk) ){

         // Площадь трапеции вычисляется через основания и высоту по формуле S = 0.5*(a+b)*h
         $Chunk['s'] = 0.5 * ($Chunk['a'] + $Chunk['b']) * $Chunk['c'];

        }
  
    }

}


function getSizeForLimit($InputArray, $LimitSize){

  $ResultArray = array('a'=>0,'b'=>0,'c'=>0,'s'=>0);

  $MaxScuare = 0;

   foreach($InputArray as $Chunk){
      if($Chunk['s'] <= $LimitSize && $Chunk['s'] > $MaxScuare){
      $MaxScuare =  $Chunk['s'];
      $ResultArray ['a'] = $Chunk['a'];
      $ResultArray ['b'] = $Chunk['b'];
      $ResultArray ['c'] = $Chunk['c'];
      $ResultArray ['s'] = $Chunk['s'];
  }
 }
   return $ResultArray;
}


function getMin($InputArray){
 
 asort($InputArray);
 return array_shift($InputArray);
}


function printTrapeze(&$InputArray){
 //столбец ODD это признак нечетности площади трапеции
 printf("\n%5s%5s%5s%5s%5s", '+-------', '+-------', '+-------', '+-------', '+-------+');
 printf("\n|%5s\t|%5s\t|%5s\t|%5s\t|%5s\t|", '"A"', '"B"', '"C"', '"S"', 'ODD');
 printf("\n%5s%5s%5s%5s%5s", '+-------', '+-------', '+-------', '+-------', '+-------+');

 foreach($InputArray as &$Chunk){
    // только при наличии всех данных можно печатать
    if(array_key_exists('a', $Chunk) 
       && array_key_exists('b', $Chunk) 
       && array_key_exists('c', $Chunk)
       && array_key_exists('s', $Chunk) ){
      $OddSquare = (fmod($Chunk['s'], 2) > 0)?' + ':'   ';

      printf("\n|%5s\t|%5s\t|%5s\t|%5s\t|%5s\t|",    
              $Chunk['a'], $Chunk['b'], $Chunk['c'], $Chunk['s'], $OddSquare );
      printf("\n%5s%5s%5s%5s%5s",    '+-------', '+-------', '+-------', '+-------', '+-------+');
        }
      }
} 


abstract class BaseMath{

  public function exp1($a, $b, $c) {
      return $a*pow($b, $c);
  }

  public function exp2($a, $b, $c) {
      
      if($b == 0){ throw new InvalidArgumentException('Ошибка! Метод exp2($a, $b, $c) не может быть вызван если $b равен 0');}
      else{
      return pow(($a/$b), $c);
      }
  }
  
  abstract protected function getValue();
    
}


class F1 extends BaseMath{   
    var $a;
    var $b;
    var $c;
    
    function __construct($a, $b, $c){
       $this -> a= $a;
       $this -> b= $b;
       $this -> c= $c;
      
    }

    public function getValue() {
       $a = $this -> a; 
       $b = $this -> b;
       $c = $this -> c;

      //что бы не получить ошибку деления на ноль в выражении (a/c)^b переменная $c не должна быть равна нулю
      if($c == 0){ throw new InvalidArgumentException('Ошибка! Метод getValue() класса F1 не может быть вызван если $this->c равен 0');}
      else{
       /*чтобы формула f=(a*(b^c)+(((a/c)^b)%3)^min(a,b,c)) в коде воспринималась лучше, разбил ее на составные части
       a*(b^c) и (((a/c)^b)%3)^min(a,b,c)*/

      /*  a*(b^c)
      Функция exp1, унаследованная от базового класса BaseMath, возвращает то, что нужно: результат вычисления a*(b^c) */
       $FirstParametr = $this->exp1($a,$b,$c); 
       
       /*(((a/c)^b)%3)^min(a,b,c)
       Тут в основание степени "напрашивается" функция exp2, но она отличается позицией одного параметра
       (a/b)^c  а нужно (a/c)^b .Не знаю опечатка в задании или нет, но exp2 не подошла*/
       $Base = pow(($a/$c), $b)%3; 
       $Exp = min($a,$b,$c);
       $SecondParametr = pow($Base, $Exp);

       return $FirstParametr + $SecondParametr;

      }
    }

}


?>