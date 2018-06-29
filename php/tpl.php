<?php
/**
 * PHP自定义函数实现assign()数组分配到模板及extract()变量分配到模板
 */
class base{
  public $array;
  public $key;
  public $val;
  public function assign($key,$val){
    if(array($val)){
      $this->array["$key"] = $val;
    }else{
      $this->array["$key"] = compact($val); // compact建立一个数组，包括变量名和它们的值
    }
  }
  public function display($tpl){
    $this->assign($this->key,$this->val);
    extract($this->array); // extract从数组中将变量导入到当前的符号表，键做变量，值做值！
    if(file_exists($tpl)){ // 模板存在就加载文件。
      include $tpl;
    }
  }
}
class indexcontroller extends base{
  public function index(){
    $arr = array('a'=>'aaaaaaa','b'=>array('a'=>'111111','b'=>'22222','c'=>'3333'),'c'=>'ccccccc','d'=>'dddddd','e'=>'eeeee');
    $str = '我是字符串';
    $this->assign('arr',$arr);
    $this->assign('str',$str);
    $this->display('index.html');
  }
}
$base = new base;
$base->index();
