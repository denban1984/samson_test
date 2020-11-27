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

?>