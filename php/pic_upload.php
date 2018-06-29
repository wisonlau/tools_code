<?php

/**
   * 多选图片上传
   * 
   * @version v1.0.0
   * @author wisonlau
   */
  public function upload()
  {
    $file = $_FILES['file'];
    empty($file) && $this->response(201,'请选择要上传的文件');
    unset($_FILES['file']);
    $count = count($file['name']);       // 上传图片的数量
    $count > 10 && $this->response(203,'批量上传图片一次最多上传10张图片');
    $tmpFile  = [];
    $returnData = [];
    for($i=0;$i<$count;$i++)          // 循环处理图片
    {
      $tmpFile['name']   = $file['name'][$i];
      $tmpFile['type']   = $file['type'][$i];
      $tmpFile['tmp_name'] = $file['tmp_name'][$i];
      $tmpFile['error']  = $file['error'][$i];
      $tmpFile['size']   = $file['size'][$i];
      $_FILES['file_'.$i] = $tmpFile;
      // 判断是否是允许的图片类型
      $ext = substr($_FILES['file_'.$i]['name'],strrpos($_FILES['file_'.$i]['name'],'.')+1); // 上传文件后缀
      stripos('jpeg|png|bmp|jpg',$ext) === FALSE && $this->response(210,'图片格式支持 JPEG、PNG、BMP格式图片');
      $data = $this->uploadOne('file_'.$i,'jpeg|png|bmp|jpg');
      if($data['status'] == 1)
      {
        $this->response(500,'第'.($i+1).'张图片上传失败，'.$data['msg']);
      }
      $returnData[$i]['url']   = $data['url'];   // 图片路径
      $returnData[$i]['old_name'] = substr($tmpFile['name'],0,strrpos($tmpFile['name'], '.')); // 图片原名称
    }
    $this->response(200,'successful',$returnData);
  }
   /**
   * 单文件上传
   * @version v1.0.0
   * @author  wisonlau
   * @param  $file   上传表单name名称
   * @param  $type   上传类型
   * @param  $maxSize 上传文件限制大小(默认 10M)
   */
  private function uploadOne($filename = 'file',$type = 'jpeg|png|bmp|jpg',$maxSize = 10240)
  {
    list($width,$height)    = getimagesize($_FILES[$filename]['tmp_name']); // 获取图片的宽和高
    list($usec, $sec) = explode(" ", microtime());
    $time = $sec.substr($usec,2);                         // 秒数+微秒数
    $ext = substr($_FILES[$filename]['name'],strrpos($_FILES[$filename]['name'],'.')+1); // 上传文件后缀
    $name   = $time.'-'.$width.'*'.$height.'.'.$ext;
    $filePath = $_FILES[$filename]['tmp_name'];
    $type   = $_FILES[$filename]['type'];
    $this->load->library('Qiniu');
    $returnData['url'] = $this->qiniu->upload($name,$filePath,$type);
    $returnData['status'] = 0;
    return $returnData;
  }
