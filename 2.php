<?php

function convertString($InputString, $SubString){
   /* �� ������� ������� ������������� ������ ������ ��������� ���������
    �.�. ������ ������ � ��������� ��������� (���� ��� ����) ������� �����������. ����� ���������� �������������� ���������� ������� ��� �����������. ������ ������� ����� ���� �������� � ���� ������
    �� ��� ����� ������ ��� ���������*/

 if(substr_count($InputString, $SubString)>1){
   //���� ��������� ������ ������ ����������� ������

   $ReversSubString = strrev($SubString); // ����������� ���������

   $SubStringLength = strlen($ReversSubString);// ���������� ����� ��������� ���������� � substr_replace()

   $PositionFirstSubString = strpos($InputString,$SubString);// ������ ��������� ���������

   $PositionSecondSubString = strpos($InputString,$SubString,$PositionFirstSubString+1);// ������ ��������� ���������

   return substr_replace($InputString,$ReversSubString,$PositionSecondSubString,$SubStringLength);//������ ��������� �� �������
  }
 else{
   //���� ��������� ���� ��� ��� ��� ����� ������ ������������ � ���������� ����
    return $InputString;
  }
}


function MyCompare($SortKey) {
  //��������� ������� ������������ ��� ���������� ������� � mySortForKey($InputArray, $SortKey)
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
     /*�������� ������� ����� $SortKey �� ���� ��������� �������. ������ ����� ���� �� ��������� ������� ����� � ��������� ������� MyCompare($SortKey) � ���������� �� ����� ����� ���� �� �� ���������� ������� ������ �������� �������� ������� � ������� ��������� ������ ���� */

    if(!array_key_exists($SortKey, $Chunk)){ throw new InvalidArgumentException('������! �� ������� ������� �� ������� '.$indx.' ����������� ���� '.$SortKey);} 
    $indx++;
    }

    /*���������� �������� usort() � �������������� ����������� ��������� ������� ��������� MyCompare($SortKey), ��������� ������� ����������� ��� ���� ����� ����� ���� ���������� ����� ���� ��� ��������� � �� ������ ������������ ������ ����*/
    usort($InputArray,MyCompare($SortKey));
    return $InputArray;
}

?>