<?php

class Tests {
    public $dbh;


    public function __construct()
    {
        set_time_limit(0);
        $dbms = 'mysql';  // 数据库类型
        $host ='127.0.0.1'; // 数据库主机名
        $dbName = 'bigdata'; // 使用的数据库
        $user = 'root';  // 数据库连接用户名
        $pass = 'root';   // 对应的密码
        $dsn = "$dbms:host=$host;dbname=$dbName";
        $this->dbh = new \PDO($dsn, $user, $pass);
        $this->dbh->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
    }

    public function test()
    {

        $sth = $this->dbh->prepare("SELECT * FROM `user`");
        $sth->execute();
        $i = 0;

        $newLine = PHP_SAPI == 'cli' ? "\n" : '<br />';

        foreach ($this->cursor($sth) as $row)
        {
            echo $row['id'] . $newLine;
            $i++;
        }

        echo "消耗内存：" . (memory_get_usage() / 1024 / 1024) . "M" . $newLine;
        echo "处理数据行数：" . $i . $newLine;
        echo "success";
    }

    public function cursor($sth)
    {
        while($row = $sth->fetch(\PDO::FETCH_ASSOC))
        {
            yield $row;
        }
    }
}

$test = new Tests();
$test->test();
