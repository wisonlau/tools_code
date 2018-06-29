<?php
/**
 * PHP实现定时监控nginx日志文件
 * 此功能是为了实现，定时监控nginx生成的日志数据，并将新增的数据提交到一个接口（比如大数据的接口，让大数据来进行分析）
 * 由于日志文件过了凌晨会切割，所以需要做一下判断，判断是第二天的日志需要从日志文件头部进行读取
 * 需要优化的逻辑：当中间进程挂了，停了一段时间，再启动时，从上次的位置重新读取，提交的数据会比较大，可能会超过提交数据大小的限制
 */
  define("MAX_SHOW", 8192*5); //新增数据提交阈值
  define("LOG_NAME", ""); //读取的日志文件
  define("LOG_SIZE", ""); //保留上次读取的位置
  define("LOG_URL", ""); //日志提交地址
  //运行时log文件原始大小
  $log_size    = get_filesize();
  $file_size     = filesize(LOG_NAME);
  if(empty($log_size)){//没有记录上次位置,则从当前位置开始
    $file_size = $file_size;
  }else if($log_size > $file_size){ //说明是第二天的日志文件，指针放到文件头
    $file_size = 0;
  }else{ //从上次记录的位置开始
    $file_size = $log_size;
  }
  $file_size_new   = 0;
  $add_size     = 0;
  $ignore_size   = 0;
  $fp = fopen(LOG_NAME, "r");
  while(1){
    clearstatcache();
    $read_num = 0;
    $file_size_new = filesize(LOG_NAME);
    $add_size = $file_size_new - $file_size;
    $add_data = array();
    $add_log = '';
    if($add_size > 0){
      //大于一个阈值提交数据
      if($add_size > MAX_SHOW){
        fseek($fp, $file_size);
        //当增加量超过8192，需要分页读取增加量
        $page = ceil($add_size/8192);
        for($i=1; $i<=$page; $i++){
          if($i == $page){//最后一页
            $end_add = $add_size - ($page -1) * 8192;
            $add_log .= fread($fp, $end_add);
          }else{
            $add_log .= fread($fp, 8192);
            $file_size_step = $file_size + $i * 8192;
            fseek($fp, $file_size_step);
          }
        }
        $add_data['add_log'] = $add_log;
        $add_data['add_log'] = base64_encode($add_data['add_log']);
        http_post(LOG_URL, $add_data);
        $file_size = $file_size_new;
        //记录当前位置
        save_filesize($file_size);
      }
    }else if($add_size < 0){ //第二天从头部开始
      $file_size = 0;
    }
    sleep(2);
  }
  fclose($fp);
  /**
   * 每次启动时获取上次打开文件位置
   */
  function get_filesize(){
    $size = file_get_contents(LOG_SIZE);
    return $size;
  }
  /**
   * 每次提交后保存这次读取文件的位置
   */
  function save_filesize($size){
    return file_put_contents(LOG_SIZE, $size);
  }
  /**
   * http请求
   * @param array $data
   * @return boolean
   */
  function http_post($url = '', $data = array())
  {
    if(empty($url)){
      return FALSE;
    }
    if($data){
      $data = http_build_query($data);
    }
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_TIMEOUT, 5 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    $return = curl_exec ( $ch );
    curl_close ( $ch );
    return $return;
  }
