<?php
// 分割代码
$i  = 0;                 //分割的块编号
$fp  = fopen("hadoop.sql","rb");      //要分割的文件
$file = fopen("split_hash.txt","a");    //记录分割的信息的文本文件，实际生产环境存在redis更合适
while(!feof($fp)){
    $handle = fopen("hadoop.{$i}.sql","wb");
    fwrite($handle,fread($fp,5242880));//切割的块大小 5m
    fwrite($file,"hadoop.{$i}.sql\r\n");
    fclose($handle);
    unset($handle);
    $i++;
}
fclose ($fp);
fclose ($file);
echo "ok";
?>

<?php
// 合并代码
$hash = file_get_contents("split_hash.txt"); //读取分割文件的信息
$list = explode("\r\n",$hash);
$fp = fopen("hadoop2.sql","ab");    //合并后的文件名
foreach($list as $value){
  if(!empty($value)) {
    $handle = fopen($value,"rb");
    fwrite($fp,fread($handle,filesize($value)));
    fclose($handle);
    unset($handle);
  }
}
fclose($fp);
echo "ok";
?>
