<?php
//组词算法
function diyWords($arr,$m){
  $result = array();
  if ($m ==1){//只剩一个词时直接返回
    return $arr;
  }
  if ($m == count($arr)){
    $result[] = implode('' , $arr);
    return $result;
  }
  $temp_firstelement = $arr[0];
  unset($arr[0]);
  $arr = array_values($arr);
  $temp_list1 = diyWords($arr, ($m-1));
  foreach ($temp_list1 as $s){
    $s = $temp_firstelement.$s;
    $result[] = $s;
  }
  $temp_list2 = diyWords($arr, $m);
  foreach ($temp_list2 as $s){
    $result[] = $s;
  }
  return $result;
}
//组词算法
$arr=array('裤子','牛仔','低腰','加肥');
$count=count($arr);
for($i=1;$i<=$count;$i++){
  $temp[$i]=diyWords($arr,$i);
}
echo '<pre/>';print_r($temp);

/*
Array
(
[1] => Array
(
[0] => 裤子
[1] => 牛仔
[2] => 低腰
[3] => 加肥
)
[2] => Array
(
[0] => 裤子牛仔
[1] => 裤子低腰
[2] => 裤子加肥
[3] => 牛仔低腰
[4] => 牛仔加肥
[5] => 低腰加肥
)
[3] => Array
(
[0] => 裤子牛仔低腰
[1] => 裤子牛仔加肥
[2] => 裤子低腰加肥
[3] => 牛仔低腰加肥
)
[4] => Array
(
[0] => 裤子牛仔低腰加肥
)
)
*/
