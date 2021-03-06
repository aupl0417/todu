<?php
/**
 * Created by PhpStorm.
 * User: aupl
 * Date: 2017/3/21
 * Time: 20:00
 */
/*
 * 用法示例

        $mydb = new MyDB();
        $where = array();//查询条件用法跟thinkphp框架中的where用法一致
//        $where1['tcg_code'] = $type;
//        $res  = $mydb->table('t_category_group')->field('tcg_id as id,tcg_depth as depth')->where($where1)->find();
//        $res  = $mydb->where($where1)->find('t_category_group', 'tcg_id as id,tcg_depth as depth');
//        $res  = $mydb->table('t_category_group')->field('tcg_id as id,tcg_depth as depth')->where($where)->order(array('tcg_id desc', 'tcg_depth asc'))->select();
//        $res = $mydb->data(['tcg_depth' => 5])->where('tcg_id=1')->save('t_category_group');
//        $res = $mydb->table('t_category_group')->data(['tcg_code' => 'aaa', 'tcg_depth' => 6, 'tcg_name' => 'abcde'])->add();
//        $res = $mydb->table('t_category_group')->field(['tcg_code', 'tcg_depth', 'tcg_name'])->data([['aaa', 6, 'abcde'], ['bbb', 5, 'ccccc']])->addAll();
        $res = $mydb->table()->where()->delete('t_category_group', array('tcg_id' => array('in', array(3))));//
        dump($res);die;


 * */

class Db extends MySql{

    protected $selectSql  = 'SELECT%DISTINCT% %FIELD% FROM %TABLE%%FORCE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT% %UNION%%LOCK%%COMMENT%';
    protected $comparison = array('eq'=>'=','neq'=>'<>','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<=','notlike'=>'NOT LIKE','like'=>'LIKE','in'=>'IN','notin'=>'NOT IN');
    protected $methods    = array('order','alias','having','group','lock','distinct','force');
    protected $options    = array();

    /*
     * 查询多条数据
     * @params $table  string       表名
     * @params $fields string/array 查询字段
     * return  array
     * */
    public function select($table = '', $fields = ''){
        $option['table']  = ($table  == '' && !empty($this->options['table'])) ? $this->options['table'] : $table;
        $option['field']  = ($fields == '' && !empty($this->options['field'])) ? $this->options['field'] : $fields;
        $option['where']  = isset($this->options['where']) && !empty($this->options['where']) ? $this->options['where'] : '';
        $option['join']   = isset($this->options['join'])  && !empty($this->options['join'])  ? $this->options['join']  : '';
        $option['order']  = isset($this->options['order']) && !empty($this->options['order']) ? $this->options['order'] : '';
        $option['limit']  = isset($this->options['limit']) && !empty($this->options['limit']) ? $this->options['limit'] : '';

        $sql   = $this->parseSql($this->selectSql, $option);
        $this->options = array();
        return $this->getAll($sql);
    }

    /*
     * 获取字段
     * @params $table  string       表名
     * @params $fields string/array 查询字段
     * @params $where  string/array 查询条件
     * @params $join   string/array 连接
     * return  string
     * */
    public function getField($table = '', $field = ''){
        $option['table']  = ($table == '' && !empty($this->options['table'])) ? $this->options['table'] : $table;
        $option['field']  = ($field == '' && !empty($this->options['field'])) ? $this->options['field'] : $field;
        $option['where']  = isset($this->options['where']) && !empty($this->options['where']) ? $this->options['where'] : '';
        $option['join']   = isset($this->options['join'])  && !empty($this->options['join'])  ? $this->options['join']  : '';
        $option['order']  = isset($this->options['order']) && !empty($this->options['order']) ? $this->options['order'] : '';
        $option['limit']  = '1';

        $sql   = $this->parseSql($this->selectSql, $option);
        $this->options = array();
        return parent::getField($sql);
    }

    /* 更新数据
     * @params $table  string       表名
     * @params $fields string/array 查询字段
     * @params $where  string/array 查询条件
     *
     * */
    public function save($table = '', $data= array()){
        $table  = ($table == '' && !empty($this->options['table'])) ? $this->options['table'] : $table;
        $where  = isset($this->options['where']) && !empty($this->options['where']) ? $this->options['where'] : '';
        $data   = isset($this->options['data']) && !empty($this->options['data']) ? $this->options['data'] : $data;
        if(!$where || !$data){
            return false;
        }

        $this->options = array();
        return $this->update($table, $data, str_replace('WHERE', '', $this->parseWhere($where))); // TODO: Change the autogenerated stub
    }


    /*
     * 查询一条数据
     * @params $table  string       表名
     * @params $fields string/array 查询字段
     * @params $where  string/array 查询条件
     *
     * */
    public function find($table = '', $field = ''){
        $option['table']  = ($table == '' && !empty($this->options['table'])) ? $this->options['table'] : $table;
        $option['field']  = ($field == '' && !empty($this->options['field'])) ? $this->options['field'] : $field;
        $option['where']  = isset($this->options['where']) && !empty($this->options['where']) ? $this->options['where'] : '';
        $option['join']   = isset($this->options['join'])  && !empty($this->options['join'])  ? $this->options['join']  : '';
        $option['order']  = isset($this->options['order']) && !empty($this->options['order']) ? $this->options['order'] : '';
        $option['limit']  = isset($this->options['limit']) && !empty($this->options['limit']) ? $this->options['limit'] : '1';

        $sql   = $this->parseSql($this->selectSql, $option);
        $this->options = array();
        return $this->getRow($sql);
    }

    /*
     * 添加一条数据
     * @params $table  string       表名
     * @params $data   array        插入数据
     *  return bool/id
     * */
    public function add($table = '', $data = array()){
        $table  = ($table == '' && !empty($this->options['table'])) ? $this->options['table'] : $table;
        $data   = isset($this->options['data']) && !empty($this->options['data']) ? $this->options['data'] : $data;
        if(!$table || !$data){
            return false;
        }

        $res = parent::insert($table, $data);
        $this->options = array();
        if($res){
            return $this->getLastID();
        }

        return $res;
    }

    /*
     * 添加多条数据
     * @params $table  string       表名
     * @params $field  string/array 表字段
     * @params $data   array        插入数据
     *  return bool/int
     * */
    public function addAll($table = '', $field = '', $data = array()){
        $table  = ($table == '' && !empty($this->options['table'])) ? $this->options['table'] : $table;
        $field  = ($field == '' && !empty($this->options['field'])) ? $this->options['field'] : $field;
        $field  = explode(',', $this->parseField($field));
        $data   = isset($this->options['data']) && !empty($this->options['data']) ? $this->options['data'] : $data;
        if(!$data){
            return false;
        }
        $this->options = array();
        return parent::inserts($table, $field, $data);
    }


    /* 删除数据
     * @params $table  string       表名
     * @params $where  string/array 查询条件
     *
     * */
    public function delete($table = '', $where= array()){

        $table  = ($table == '' && !empty($this->options['table'])) ? $this->options['table'] : $table;
        $where  = isset($this->options['where']) && !empty($this->options['where']) ? $this->options['where'] : $where;
        $where  = $this->parseWhere($where);

        if(!$where){
            return false;
        }
        $this->options = array();
        $res = parent::delete($table, str_replace('WHERE', '', $this->parseWhere($where))); // TODO: Change the autogenerated stub
        if($res){
            return true;
        }
        return $res;
    }


    private function setField($field, $value){
        if(is_array($value) && $value[0] == 'exp'){
            $data[$field]   = $value[1];
        }else{
            $data[$field]   = $value;
        }

        $sql = 'UPDATE ' . $this->options['table'] . ' SET ' . $field . '=' . $data[$field] . ' ' . $this->parseWhere($this->options['where']);
        return $this->exec($sql);
    }

    /*
     * 增加$step
     * @params $field string 操作字段
     * @params $step  int    增量
     * return bool
     * */
    public function setInc($field, $step=1) {
        return $this->setField($field, array('exp', $field . '+' . intval($step)));
    }

    /*
     * 减少$step
     * @params $field string 操作字段
     * @params $step  int    减量
     * return bool
     * */
    public function setDec($field, $step=1) {
        return $this->setField($field,array('exp', $field . '-' . intval($step)));
    }

    /*
	* value 分析
	* @access protected
	* @param mixed $value
	* @return string
	*/
    protected function parseValue($value){
        if(is_string($value)){
            $value = "'" . $this->escapeString($value) . "'";
        }elseif(isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp'){//表达式
            $value = $this->escapeString($value[1]);
        }elseif(is_array($value)){
            $value = array_map(array($this, 'parseValue'), $value);
        }elseif(is_bool($value)){
            $value = $value ? '1' : '0';
        }elseif(is_null($value)){
            $value = 'null';
        }

        return $value;
    }

    /*
    * field分析
    * @access protected
    * @param mixed $fields
    * @return string
    */
    protected function parseField($fields){
        if(is_string($fields) && $fields !== ''){
            $fields = explode(',', $fields);
        }

        if(is_array($fields)){
            $array = array();
            foreach($fields as $key=>$field){
                if(!is_numeric($key)){
                    $array[] = $this->parseKey($key) . ' AS ' . $this->parseKey($field);
                }else {
                    $array[] = $this->parseKey($field);
                }
            }
            $fieldsStr = implode(',', $array);
        }else {
            $fieldsStr = '*';
        }

        return $fieldsStr;
    }

    /*
    * order 分析
    * @access protected
    * @param mixed $order
    * @return string
    */
    protected function parseOrder($order){
        if(is_array($order)){
            $array = array();
            foreach($order as $key=>$val){
                if(is_numeric($key)){
                    $array[] = $this->parseKey($val);
                }else {
                    $array[] = $this->parseKey($key) . ' ' . $val;
                }
            }
            $order = implode(',', $array);
        }

        return $order ? ' ORDER BY ' . $order : '';
    }

    /*
    * limit 分析
    * @access protected
    * @param mixed $limit
    * @return string
    */
    protected function parseLimit($limit){
        return $limit ? ' LIMIT ' . $limit . ' ' : '';
    }

    /*
    * where 分析
    * @access protected
    * @param mixed $where
    * @return string
    */
    protected function parseWhere($where){
        $whereStr = '';
        if(is_string($where)){
            $whereStr = $where;
        }else {
            $operate = isset($where['_logic']) ? strtoupper($where['_logic']) : '';
            if(in_array($operate, array('OR', 'AND', 'XOR'))){
                $operate = ' ' . $operate . ' ';
                unset($where['_logic']);
            }else {
                $operate = ' AND ';
            }
            foreach($where as $key=>$val){
                if(is_numeric($key)){
                    $key = '_complex';
                }
                if(0 === strpos($key, '_')){
                    $whereStr .= $this->parseThinkWhere($key, $val);
                }else {
                    $key = trim($key);
                    $whereStr .= $this->parseWhereItem($this->parseKey($key), $val);
                }
                $whereStr .= $operate;
            }
            $whereStr = substr($whereStr, 0, -strlen($operate));
        }
        return empty($whereStr) ? '' : ' WHERE ' . $whereStr;
    }

    /*
    * join 分析
    * @access protected
    * @param mixed $join
    * @return string
    */
    protected function parseJoin($join){
        if(is_array($join)){
            $join = ' ' . implode(' ', $join) . ' ';
        }

        return $join;
    }

    protected function parseWhereItem($key, $val){
        $whereStr = '';
        if(is_array($val)){
            if(is_string($val[0])){
                if(preg_match('/^(EQ|NEQ|GT|EGT|LT|ELT)$/i', $val[0])){ //比较运算
                    $whereStr .= $key . ' ' . $this->comparison[strtolower($val[0])] . ' ' . $this->parseValue($val[1]);
                }elseif(preg_match('/^(NOTLIKE|LIKE)$/i', $val[0])){//模糊查找
                    if(is_array($val[1])){
                        $likeLogic = isset($val[2]) ? strtoupper($val[2]) : 'OR';
                        if(in_array($likeLogic, array('AND', 'OR', 'XOR'))){
                            $likeStr = $this->comparison[strtolower($val[0])];
                            $like = array();
                            foreach($val[1] as $item){
                                $like[] = $key . ' ' . $likeStr . ' ' . $this->parseValue($item);
                            }
                            $whereStr .= '(' . implode(' ' . $likeLogic . ' ', $like) . ')';
                        }
                    }else {
                        $whereStr .= $key . ' ' . $this->comparison[strtolower($val[0])] . ' ' . $this->parseValue($val[1]);
                    }
                }elseif('exp' == strtolower($val[0])){//表达式
                    $whereStr .= $key . ' ' . $val[1];
                }elseif(preg_match('/IN/i', $val[0])){//IN
                    if(isset($val[2]) && 'exp' == $val[2]){
                        $whereStr .= $key . ' ' . strtolower($val[0]) . ' ' . $val[1];
                    }else {
                        if(is_string($val[1])){
                            $val[1] = explode(',', $val[1]);
                        }
                        $zone 		= implode(',', $this->parseValue($val[1]));
                        $whereStr  .= $key . ' ' . $this->comparison[strtolower($val[0])] . ' (' . $zone . ')';
                    }
                }elseif(preg_match('/BETWEEN/i', $val[0])){
                    $data 		= is_string($val[1]) ? explode(',', $val[1]) : $val[1];
                    $whereStr  .= $key . ' ' . strtolower($val[0]) . ' ' . $this->parseValue($data[0]) . ' AND ' . $this->parseValue($data[1]);
                }
            }
        }else {
            $whereStr .= $key . ' = ' . $this->parseValue($val);
        }

        return $whereStr;
    }

    protected function parseThinkWhere($key, $val){
        $whereStr = '';
        switch($key){
            case '_string':
                $whereStr = $val;
                break;
            case '_complex':
                $whereStr = substr($this->parseWhere($val), 6);
                break;
            case '_query':
                parse_str($val, $where);
                if(isset($where['_logic'])){
                    $op = ' ' . strtoupper($where['_logic']) . ' ';
                    unset($where['_logic']);
                }else {
                    $op = ' AND ';
                }
                $array = array();
                foreach($where as $field=>$data){
                    $array[] = $this->parseKey($field) . ' = ' . $this->parseValue($data);
                }
                $whereStr = implode($op, $array);
                break;
        }
        return '( ' . $whereStr . ' )';
    }

    protected function parseKey(&$key){
        $key = trim($key);

        if(!is_numeric($key) && !preg_match('/[,\'\"\*\(\)`.\s]/',$key)) {
            $key = '`' . $key . '`';
        }

        return $key;
    }

    /*
     * 设置锁机制
     * @access protected
     * @return string
     */
    protected function parseLock($lock=false) {
        return $lock?   ' FOR UPDATE '  :   '';
    }

    /*
     * index分析，可在操作链中指定需要强制使用的索引
     * @access protected
     * @param mixed $index
     * @return string
     */
    protected function parseForce($index) {
        if(empty($index)) return '';
        if(is_array($index)) $index = join(",", $index);
        return sprintf(" FORCE INDEX ( %s ) ", $index);
    }

    /*
     * 替换SQL语句中表达式
     * @access public
     * @param array $options 表达式
     * @return string
     */
    public function parseSql($sql, $options=array()){
        $sql   = str_replace(
            array('%TABLE%','%DISTINCT%','%FIELD%','%JOIN%','%WHERE%','%GROUP%','%HAVING%','%ORDER%','%LIMIT%','%UNION%','%LOCK%','%COMMENT%','%FORCE%'),
            array(
                $this->parseTable($options['table']),
                $this->parseDistinct(isset($options['distinct']) ? $options['distinct'] : ((isset($this->options['distinct']) && !empty($this->options['distinct'])) ? $this->options['distinct'] : false)),
                $this->parseField(!empty($options['field']) ? $options['field'] : '*'),
                $this->parseJoin(!empty($options['join']) ? $options['join'] : ((isset($this->options['join']) && !empty($this->options['join'])) ? $this->options['join'] : '')),
                $this->parseWhere(!empty($options['where']) ? $options['where'] : ((isset($this->options['where']) && !empty($this->options['where'])) ? $this->options['where'] : '')),
                $this->parseGroup(!empty($options['group']) ? $options['group'] : ((isset($this->options['group']) && !empty($this->options['group'])) ? $this->options['group'] : '')),
                $this->parseHaving(!empty($options['having']) ? $options['having'] : ((isset($this->options['having']) && !empty($this->options['having'])) ? $this->options['having'] : '')),
                $this->parseOrder(!empty($options['order']) ? $options['order'] : ((isset($this->options['order']) && !empty($this->options['order'])) ? $this->options['order'] : '')),
                $this->parseLimit(!empty($options['limit']) ? $options['limit'] : ((isset($this->options['limit']) && !empty($this->options['limit'])) ? $this->options['limit'] : '')),
                $this->parseLock(isset($options['lock']) ? $options['lock'] : false),
                $this->parseComment(!empty($options['comment']) ? $options['comment'] : ''),
                $this->parseForce(!empty($options['force']) ? $options['force'] : '')
            ),$sql);

        return $sql;
    }


    /*
     * group分析
     * @access protected
     * @param mixed $group
     * @return string
     */
    protected function parseGroup($group) {
        return !empty($group)? ' GROUP BY '.$group:'';
    }

    /*
     * having分析
     * @access protected
     * @param string $having
     * @return string
     */
    protected function parseHaving($having) {
        return  !empty($having)?   ' HAVING '.$having:'';
    }

    /*
     * comment分析
     * @access protected
     * @param string $comment
     * @return string
     */
    protected function parseComment($comment) {
        return  !empty($comment)?   ' /* '.$comment.' */':'';
    }

    /*
     * distinct分析
     * @access protected
     * @param mixed $distinct
     * @return string
     */
    protected function parseDistinct($distinct) {
        return !empty($distinct)?   ' DISTINCT ' :'';
    }

    public function parseTable($table){
        return $table ? $table : false;
    }

    protected function escapeString($value){
        return addslashes($value);
    }

    public function limit($options = ''){
        $this->options['limit'] = $options;
        return $this;
    }

    public function where($options = array()){
        $this->options['where'] = $options;
        return $this;
    }

    public function field($options = ''){
        $this->options['field'] = $options;
        return $this;
    }

    /*
     * 查询SQL组装 join
     * @access public
     * @param mixed $join
     * @param string $type JOIN类型
     * @return object
     */
    public function join($join,$type='INNER') {
        if(is_array($join)) {
            foreach ($join as $key=>&$_join){
                $_join  =   false !== stripos($_join,'JOIN')? $_join : $type.' JOIN ' . $_join;
            }
        }
        $this->options['join']      =   $join;

        return $this;
    }

    /*
     * 设置数据对象值
     * @access public
     * @param mixed $data 数据
     * @return object
     */
    public function data($data=''){
        if('' === $data && !empty($this->options['data'])) {
            return $this->options['data'];
        }
        if(is_object($data)){
            $data   =   get_object_vars($data);
        }elseif(is_string($data)){
            parse_str($data,$data);
        }elseif(!is_array($data)){
            exit('请用数组格式');
        }
        $this->options['data'] = $data;
        return $this;
    }

    public function table($table = ''){
        $this->options['table'] =   $table;
        return $this;
    }

    public function __destruct()
    {
        parent::__destruct(); // TODO: Change the autogenerated stub
        $this->options = null;
    }

    /*
     * 利用__call方法实现一些特殊的方法
     * @access public
     * @param string $method 方法名称
     * @param array $args 调用参数
     * @return mixed
     */
    public function __call($method,$args) {
        if(in_array(strtolower($method),$this->methods,true)) { // 连贯操作的实现
            $this->options[strtolower($method)] =   $args[0];
            return $this;
        }elseif(in_array(strtolower($method),array('getcount','sum','min','max','avg'),true)){// 统计查询的实现
            $field =  isset($args[0]) ? $args[0] : '*';
            if(strpos(strtolower($method), 'count') > 0){
                $method = 'count';
            }
            return $this->getField('', strtoupper($method).'('.$field.') AS t_'.$method);
        }elseif(strtolower(substr($method, 0, 5))=='getby') { // 根据某个字段获取记录
            $tableFields = $this->getTableFields($this->options['table']);
            $tableFields = array_column($tableFields, 'Field');//表字段集

            $fields = array_map(function($value){//表字段去除下划线并转为小写
                return strtolower(str_replace('_', '', $value));//表字段集
            }, $tableFields);

            $key   = array_search(strtolower(substr($method,5)), $fields);//确定在表字段集里的位置
            $field = $tableFields[$key];    //得到的具体字段
            $where[$field] =  $args[0];
            return $this->where($where)->field($tableFields)->find();
        }elseif(strtolower(substr($method, 0, 10))=='getfieldby') { // 根据某个字段获取记录的某个值
            $tableFields = $this->getTableFields($this->options['table']);
            $tableFields = array_column($tableFields, 'Field');//表字段集
            
            $fields = array_map(function($value){//表字段去除下划线并转为小写
                return strtolower(str_replace('_', '', $value));//表字段集
            }, $tableFields);

            $key   = array_search(strtolower(substr($method,10)), $fields);//确定在表字段集里的位置
            $name = $tableFields[$key];    //得到的具体字段
            $where[$name] = $args[0];

            return $this->where($where)->getField('', $args[1]);
        }else{
            return;
        }
    }


}