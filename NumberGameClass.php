<?php

/*
 * author : Veselin Doynov
 */

class NumberGame {
    
    protected $number1;
    protected $number2;
    protected $multiplicationResult = array();
    protected $multiplicationResultData = array();
    protected $multiplicationFinal = array();
    protected $floats = 0;

    public function __construct($number1, $number2){
        
        
        list($number1, $number2) = $this->prepareNumbers($number1, $number2);
        
        $this->number1 = array_reverse(str_split($number1));
        $this->number2 = array_reverse(str_split($number2));
        $this->multiplicationProcess();
    }
    
    protected function prepareNumbers($number1 ,$number2){
        
        $number1 = (string)$number1;
        $number2 = (string)$number2;
        $number1 = str_replace(',', '.', $number1);
        $number2 = str_replace(',', '.', $number2);
        
        $number1 = $this->fixInput($number1);
        $number2 = $this->fixInput($number2);
        
        list($number1, $number2) = $this->calculateFloat($number1, $number2);
        
        return array($number1, $number2);
    }
    
    protected function fixInput($input){
        
        $pattern = '/^[0-9]+$/';
        $firstDot = false;
        $input = str_split($input);
        $input = array_reverse($input);
        if($input[0] == '.')
            array_unshift ($input, 0);
        $input = array_reverse($input);
        
        $output = array();
        for($i=0; $i < count($input); $i++){
            if($input[$i]=='.' && !$firstDot) {
                $firstDot = true;
                $output[] = $input[$i];
            }
            
            if(preg_match($pattern, $input[$i]))
                    $output[] = $input[$i];
        }
        
        return implode($output);
    }
    
    protected function calculateFloat($number1, $number2){
        
        $number1Exp = explode('.', $number1);
        $number2Exp = explode('.', $number2);
        
        $num1 = 0;
        $num2 = 0;
        
        if(isset($number1Exp[1])){
            $num1 = count(str_split($number1Exp[1]));
            $number1 = str_replace('.','', $number1);
            $number1 = $this->removeZeroes($number1);
        }
        
        if(isset($number2Exp[1])){
            $num2 = count(str_split($number2Exp[1]));
            $number2 = str_replace('.','', $number2);
            $number2 = $this->removeZeroes($number2);
        }
       
        if($num1 && $num2)
            $this->floats = $num1 + $num2;
        if(!$num1 && $num2)
            $this->floats = $num2;
        if($num1 && !$num2)
            $this->floats = $num1;
        
        
        return array($number1, $number2);
    }
    
    
    protected function removeZeroes($input){
        
        $input = str_split($input);
        $output = array();
        $zeroBreak = false;
        foreach($input as $value)
            if($value == 0 && !$zeroBreak) {
                continue;
            }else {
                $zeroBreak = true;
                $output[] = $value;
            }
        
        return implode($output);    
    }
    
    protected function returnSmallerFirst(){
        
        //loop the smaller number
        $loop = $this->number1;
        $multiple = $this->number2;
        
        $number1 = (int)implode(array_reverse($this->number1));
        $number2 = (int)implode(array_reverse($this->number2));
        
        if($number1 > $number2){
            $loop = $this->number2;
            $multiple = $this->number1;
        }
        
        return array($loop, $multiple);
    }
    
    protected function multiplicationProcess(){
        
        list($loop, $multiple) = $this->returnSmallerFirst();
        
        $this->generateMultiplicationData($loop, $multiple);
        
        $this->accumulateMultiplicationData();
        
        $this->formatResult();
    }
    
    protected function generateMultiplicationData($loop, $multiple){
        
        for($i=0;$i < count($loop);$i++){
            $temp = array_reverse($this->multipleByNumber($multiple, $loop[$i]));
           
            for($j=0; $j < $i; $j++)
                $temp[] = 0;
            
            $this->multiplicationResultData[] = $temp;
        }
    }
    
    protected function formatResult(){
        
        $final = array(); 
        $this->multiplicationFinal = array_reverse($this->multiplicationFinal);
        $count = count($this->multiplicationFinal);
        if($count > $this->floats && $this->floats){
            $tempResult = implode($this->multiplicationFinal);
            $tempResult = substr($tempResult, 0, $count - $this->floats) . '.' . substr($tempResult, $count - $this->floats);
            $this->multiplicationFinal = str_split($tempResult);
         }elseif($this->floats) {
            $zero = $this->floats - $count;
            for($i=0; $i < $zero; $i++)
                array_unshift ($this->multiplicationFinal, 0);
             array_unshift ($this->multiplicationFinal, '.');
             array_unshift ($this->multiplicationFinal, 0);
        }
    }
    
    protected function accumulateMultiplicationData(){
        
        $carry = 0;
        for($i=0 ; $i < count($this->multiplicationResultData); $i++){
            $temp = array_reverse($this->multiplicationResultData[$i]);
            for($j=0; $j < count($temp); $j++){
                if(!isset($this->multiplicationFinal[$j])){
                    $this->multiplicationFinal[$j] = 0;
                 }
                
                $sum = $this->multiplicationFinal[$j] + $temp[$j] + $carry;
                $this->multiplicationFinal[$j] = $sum%10;
                $carry = 0;
                if($sum >= 10)
                   $carry = 1;
            }
            if($carry)
                $this->multiplicationFinal[] = $carry;
        }
    }
    
    protected function multipleByNumber($number, $digit){
        
        $carry = 0;
        $this->multiplicationResult = array();
       
        foreach($number as $ndigit){
            $temp = $ndigit*$digit + $carry;
            $this->multiplicationResult[] = $temp%10;
            $carry = floor($temp/10);
        }
        
        if($carry)
            $this->multiplicationResult[] = $carry;
        
        return $this->multiplicationResult;
    }
    
    public function getMultipicationResult (){
        
        return $this->multiplicationResult;
    }
    
    public function getMultipicationResultData (){
        
        return $this->multiplicationResultData;
    }
    
    public function getMultiplicationFinal(){
        
        return $this->multiplicationFinal;
    }
    
    
    public function getResult(){
        
        return implode($this->multiplicationFinal);
    }
    
    public function printMultiplicationResult($string = true){
        
        if($string)
            echo $this->getResult ();
        else {
            print '<pre>';
            print_r($this->multiplicationFinal);
            print '</pre>';
        }
    }
    
}
