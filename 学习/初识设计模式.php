<?php

	namespace struct;
	//数据结构
	//栈
	$stack=new \splStack();
	$stack->push('one');	//入栈
	$stack->push('two');
	echo $stack->pop();		//出栈
	echo $stack->pop();
	//队列
	$queue=new \splQueue();
	$queue->enqueue('three');//入队
	$queue->enqueue('four');
	echo $queue->dequeue();	 //出队
	echo $queue->dequeue();	 
	//(最小)堆
	$heap=new \splMinHeap();
	$heap->insert('five');
	$heap->insert('six');
	echo $heap->extract();
	echo $heap->extract();
	//固定尺寸的数组
	$sarr=new \splFixedArray(10);
	$sarr[0]='a';
	$sarr[8]='c';
	var_dump($sarr);  //固定10个长度，无论有没有使用都会分配空间
	//(连贯)链式操作
	class db{
		public $where='';
		public $limit='';
		public $select='';
		public $field=array();
		function where($where=''){
			$this->where=$where;
			return $this;				//主要返回当前实例化后对象
		}
		function limit($limit=''){
			$this->limit=$limit;
			return $this;
		}
		function select($select=''){
			$this->select=$select;
			$sql=$this->select.' '.$this->where.' '.$this->limit;
			echo $sql;
		}
		//魔术方法
		//设置获取不存在的属性 
		function __set($key,$val){
			$this->field[$key]=$val;
		}
		function __get($key){
			if(empty($this->field[$key]))
				return '';
			else
				return $this->field[$key];
		}
		//调用不存在的方法
		function __call($funName,$arg){
			echo $funName;
			print_r($arg);
		}
		//调用不存在的静态方法
		static function __callStatic($funName,$arg){
			echo 'static'.$funName;
			print_r($arg);
		}
		//对象转成字符串
		function __toString(){
			return '当前类是'.__CLASS__;  //必须return
		}
		//当把对象当成函数调用时
		function __invoke($arg1,$arg2,$agr3){  //......
			return __FUNCTION__.$arg1.$arg2;
		}
	}
	$db=new db();
	$db->where('where id>10')->limit('limit 10')->select('select * from test');
	//调用、设置不存在的属性
	$db->name='zhang';
	echo "\n".$db->name;
	//调用不存在的方法(、静态)
	$db->sum('a',123);
	$db::add('b','456');
	//对象当成字符串
	echo $db;
	//对象当成函数
	echo $db('a','b','c');

	//设计模式开始
	namespace design;
	use struct\db;
	/**
	*工厂模式 (要什么对象就返回什么类的对象)
	*好处：更改类名只需更改一处(其他是设计模式的基础)
	*/
	class Factory{
		private static $object;
		static function createDB(){
			return new db();
		} 
	}
	/*单例模式 (同一个类实例化的对象只允许出现一次)
	 *好处：节省内存开销
	*/
	class Singleton{
		private static $obj;
		private function __construct(){}
		private function __clone(){}
		public static function getDB(){
			if(empty(self::$obj)){
				return self::$obj=Factory::createDB();
			}else{
				return self::$obj;
			}
		}
	}
	/*注册器模式 (将已经实例化的类保存在全局的静态变量中)
	 *好处：所有的类实例化后不需在实例化，想用就在里面拿，不想用就删
	*/
	class register{
		public static $object=array();
		static function set($ailas,$obj){
			self::$object[$ailas]=$obj;
		}
		static function get($ailas){
			return self::$object[$ailas];
		}
		static function del($ailas){
			unset(self::$object[$ailas]);
		}
	}
	/**
	* 工厂单例模式 + 注册器
	*/
	class FactorySing{
		private static $db;
		private function __construct(){

		}
		private function __clone(){

		}
		static function createDB(){
			if(empty(self::$db)){
				self::$db=new db();
				register::set('db',self::$db); //在此处进行注册
				return self::$db;
			}else{
				return self::$db;
			}
		}
	}
	$db=FactorySing::createDB();
	var_dump($db);
	$db=FactorySing::createDB();
	var_dump($db);
	//获取注册的db类
	var_dump(register::get('db'));


	//适配器模式(对多种API实现统一)
	//**好处：将所有类似功能的API(mysql,mysqli,pdo)封装成有统一方法属性的类(调用方便)
	interface dbcomm{
		function connect($host,$user,$pass,$dbname);
		function query($sql);
		function close();
	}
	class mysql implements dbcomm{
		public $conn;
		public function connect($host,$user,$pass,$dbname){

			$this->conn=\mysql_connect($host,$user,$pass);
			mysql_select_db($dbname);
		}
		public function query($sql){
			return \mysql_query($sql,$this->connect);
		}
		public function close(){
			\mysql_close($this->connect);
		}
	}
	class mysqli implements dbcomm{
		public $conn;
		public function connect($host,$user,$pass,$dbname){

			$this->conn=new \mysqli($host,$user,$pass,$dbname);
		}
		public function query($sql){
			return $this->conn->query($sql);
		}
		public function close(){
			$this->connect->close();
		}
	}

	class pdo implements dbcomm{
		public $conn;
		public function connect($host,$user,$pass,$dbname){

			$this->conn=new \pdo("mysql:host=$host;dbname=$dbname",$user,$pass);
		}
		public function query($sql){
			return $this->conn->query($sql);
		}
		public function close(){
			unset($this->connect);
		}
	}
	$db=new pdo();
	$db->connect('127.0.0.1','root','admin','test');
	$res=$db->query('select * from test');
	print_r($res->fetchAll(\PDO::FETCH_OBJ));

	/**策略者模式
	*替换传统if else硬编码 使代码更好维护
	*/
	namespace One;
	//策略接口
	interface CatStrategy{
		Const FAMILY="猫科";
		function cry();
		function run();
	}
	//第一种实例
	class Cat implements CatStrategy{
		function cry(){
			echo '喵喵';
		}
		function run(){
			echo 'slow';
		}
	}
	//第二种实例
	class Tiger implements CatStrategy{
		function cry(){
			echo '喔噢';
		}
		function run(){
			echo 'speed';
		}
	}
	namespace Two;
	use One\Cat;
	use One\Tiger;
	//根据状况展示实例
	class Show{
		public static $obj;
		public function setStrategy(\One\CatStrategy $obj){
			return self::$obj=$obj;
		}
		public function index(){
			echo 'this is'.self::$obj::FAMILY."\n";
			echo 'it cry is '.self::$obj->cry()."\n";
			echo 'it run is '.self::$obj->run()."\n";
		}
	}
	//$_GET['name']='mao';
	if(!empty($_GET['name'])){
		$obj=new Cat;
	}else{
		$obj=new Tiger;
	}
	$show=new Show;
	$show->setStrategy($obj);
	$show->index();

	/**
	*数据对象映射模式
	*是将对象和数据存储映射起来，对一个对象的操作会映射为对数据存储的操
	*作。在代码中实现数据对象映射模式，实现一个ORM类，将复杂的sql语句映
	*射成对象属性的操作。对象关系映射（Object Relational Mapping，ORM）
	*/
	class UserORM{
		public $mobile;
		public $email;
		public $db;
		public $id;
		public function __construct($id){
			$this->id=$id;
			$this->db=new \mysqli('127.0.0.1','root','admin','temp');
			$sql='select * from jc_user where id='.$id;
			$res=$this->db->query($sql);
			$row=$res->fetch_assoc();
			$this->mobile=$row['mobile'];
			$this->email=$row['email'];
		}
		public function __destruct(){
			$sql="update jc_user set mobile='$this->mobile',email='$this->email' where id=$this->id";
			$this->db->query($sql);
		}
		public function show(){
			$res=$this->db->query('select id,mobile,email from jc_user where id='.$this->id);
			print_r($res->fetch_assoc());
		}
	}
	$user=new UserORM(1);
	$user->mobile='1235';
	$user->email='123@';
	$user->show();

?>