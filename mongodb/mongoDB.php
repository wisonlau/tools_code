<?php
/**
 * PHP操作mongodb数据库操作类
 * MongoDB是一个介于关系数据库和非关系数据库之间的产品，是非关系数据库当中功能最丰富，最像关系数据库的。他支持的数据结构非常松散，是类似json的bjson格式，因此可以存储比较复杂的数据类型。Mongo最大的特点是他支持的查询语言非常强大，其语法有点类似于面向对象的查询语言，几乎可以实现类似关系数据库单表查询的绝大部分功能，而且还支持对数据建立索引。
 */
class Database {
  protected $database  = '';
  protected $mo;
  /**
   * 构造方法
   */
  public function __construct() {
    $server = DBSERVER;
    $user = DBUSER;
    $password = DBPASS;
    $port = DBPORT;
    $database = DBNAME;
    $mongo = $this->getInstance($server, $user, $password, $port);
    $this->database = $mongo->$database;
  }
  /**
   * 数据库单例方法
   * @param $server
   * @param $user
   * @param $password
   * @param $port
   * @return Mongo
   */
  public function getInstance($server, $user, $password, $port) {
    if (isset($this->mo)) {
      return $this->mo;
    } else {
      if (!empty($server)) {
        if (!empty($port)) {
          if (!empty($user) && !empty($password)) {
            $this->mo = new Mongo("mongodb://{$user}:{$password}@{$server}:{$port}");
          } else {
            $this->mo = new Mongo("mongodb://{$server}:{$port}");
          }
        } else {
          $this->mo = new Mongo("mongodb://{$server}");
        }
      } else {
        $this->mo = new Mongo();
      }
      return $this->mo;
    }
  }
  /**
   * 查询表中所有数据
   * @param $table
   * @param array $where
   * @param array $sort
   * @param string $limit
   * @param string $skip
   * @return array|int
   */
  public function getAll($table, $where = array(), $sort = array(), $limit = '', $skip = '') {
    if (!empty($where)) {
      $data = $this->database->$table->find($where);
    } else {
      $data = $this->database->$table->find();
    }
    if (!empty($sort)) {
      $data = $data->sort($sort);
    }
    if (!empty($limit)) {
      $data = $data->limit($limit);
    }
    if (!empty($skip)) {
      $data = $data->skip($skip);
    }
    $newData = array();
    while ($data->hasNext()) {
      $newData[] = $data->getNext();
    }
    if (count($newData) == 0) {
      return 0;
    }
    return $newData;
  }
  /**
   * 查询指定一条数据
   * @param $table
   * @param array $where
   * @return int
   */
  public function getOne($table, $where = array()) {
    if (!empty($where)) {
      $data = $this->database->$table->findOne($where);
    } else {
      $data = $this->database->$table->findOne();
    }
    return $data;
  }
  /**
   * 统计个数
   * @param $table
   * @param array $where
   * @return mixed
   */
  public function getCount($table, $where = array()) {
    if (!empty($where)) {
      $data = $this->database->$table->find($where)->count();
    } else {
      $data = $this->database->$table->find()->count();
    }
    return $data;
  }
  /**
   * 直接执行mongo命令
   * @param $sql
   * @return array
   */
  public function toExcute($sql) {
    $result = $this->database->execute($sql);
    return $result;
  }
  /**
   * 分组统计个数
   * @param $table
   * @param $where
   * @param $field
   */
  public function groupCount($table, $where, $field) {
    $cond = array(
      array(
        '$match' => $where,
      ),
      array(
        '$group' => array(
          '_id' => '$' . $field,
          'count' => array('$sum' => 1),
        ),
      ),
      array(
        '$sort' => array("count" => -1),
      ),
    );
    $this->database->$table->aggregate($cond);
  }
  /**
   * 删除数据
   * @param $table
   * @param $where
   * @return array|bool
   */
  public function toDelete($table, $where) {
    $re = $this->database->$table->remove($where);
    return $re;
  }
  /**
   * 插入数据
   * @param $table
   * @param $data
   * @return array|bool
   */
  public function toInsert($table, $data) {
    $re = $this->database->$table->insert($data);
    return $re;
  }
  /**
   * 更新数据
   * @param $table
   * @param $where
   * @param $data
   * @return bool
   */
  public function toUpdate($table, $where, $data) {
    $re = $this->database->$table->update($where, array('$set' => $data));
    return $re;
  }
  /**
   * 获取唯一数据
   * @param $table
   * @param $key
   * @return array
   */
  public function distinctData($table, $key, $query = array()) {
    if (!empty($query)) {
      $where = array('distinct' => $table, 'key' => $key, 'query' => $query);
    } else {
      $where = array('distinct' => $table, 'key' => $key);
    }
    $data = $this->database->command($where);
    return $data['values'];
  }
}
