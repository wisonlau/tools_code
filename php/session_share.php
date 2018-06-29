php.ini
设置方法有2种:
1.
vi /etc/php.ini
session.save_handler = memcache
session.save_path = "tcp://192.168.20.193:11211,tcp://192.168.20.194:11211"

2.
<?php
ini_set("session.save_handler", "memcache");
ini_set("session.save_path", "tcp://192.168.20.193:11211,tcp://192.168.20.194:11211");
?>

<?php
  // login.php
  session_start();
  $_SESSION['login_time'] = time();
  $_SESSION['username'] = 'test2';
  $token=session_id();
  echo $token;
  //memache实现
  $mem = new Memcache();
  $mem->addServer('192.168.20.193',11211);
  $mem->addServer('192.168.20.194',11211);
  /*
  //memached实现
  $mem = new Memcached();
  $servers = array(
   array('192.168.20.193', 11211, 33),
   array('192.168.20.194', 11211, 67)
  );
  $mem->addServers($servers);
  */
  echo '<hr>';
  print_r($mem->get($token));
  ?>
  <p>
  <a href="http://192.168.20.194/user.php?token=<?php echo $token;?>" rel="external nofollow" target="_balnk">跳转到194网站的个人中心</a>
  </p>
?>

<?php
  // user.php
  $mem = new Memcache();
  $mem->addServer('192.168.20.193',11211);
  $mem->addServer('192.168.20.194',11211);
  $token=$_GET['token'];//获取传过来的token
  print_r($mem->get($token));
  ?>
  <p>
  <a href="http://192.168.20.193/user.php?token=<?php echo $token;?>" rel="external nofollow" target="_balnk">返回193网站的个人中心</a>
  </p>
?>
