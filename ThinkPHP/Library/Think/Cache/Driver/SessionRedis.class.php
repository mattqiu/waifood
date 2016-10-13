<?php
/**
 * Description: 自定义ThinkPHP中Redis驱动
 * Site: http://www.lyblog.net
 */
defined('THINK_PATH') or exit();

/**
 * Redis方式Session驱动
 * @category Extend
 * @package Extend
 * @subpackage Driver.Session
 * @author liu21st <liu21st@gmail.com>
 */
class SessionRedis {

    /**
     * Session有效时间
     */
    protected $lifeTime = 86400;

    /**
     * 保存Redis连接对象
     */
    protected $redis = '';

    /**
     * Session 的配置
     */
    protected $options = array();

    public  function __construct(){
        $this->options = C('SESSION_OPTIONS');
    }

    /**
     * 打开Session
     * @access public
     * @param string $savePath
     * @param mixed $sessName
     */
    public function open($savePath, $sessName) {
        $this->lifeTime = $this->options['expire'] ? $this->options['expire'] : $this->lifeTime;
        $db = $this->options['db']?$this->options['db']:0;
        $this->redis = CacheRedis::getInstance(array("timeout"=>$this->lifeTime,"db"=>$db));
        if(!empty($this->redis)){
            return true;
        }
        return false;
    }

    /**
     * 关闭Session
     * @access public
     */
    public function close() {
        return $this->redis->close();
    }

    /**
     * 读取Session
     * @access public
     * @param string $sessID
     */
    public function read($sessID) {
        if(isset($this->options['prefix'])){
           $sessID = $this->options['prefix'] . $sessID;
        }
        $data = $this->redis->get($sessID);
        return $data ? $data : '';
    }

    /**
     * 写入Session
     * @access public
     * @param string $sessID
     * @param String $sessData
     */
    public function write($sessID, $sessData) {
        $sessID = $this->options['prefix'] . $sessID;
        return $this->redis->set($sessID, $sessData, $this->lifeTime);
    }

    /**
     * 删除Session
     * @access public
     * @param string $sessID
     */
    public function destroy($sessID) {
        $sessID = $this->options['prefix'] . $sessID;
        return $this->redis->delete($sessID);
    }

    /**
     * Session 垃圾回收
     * @access public
     * @param string $sessMaxLifeTime
     */
    public function gc($sessMaxLifeTime) {
        return true;
    }

    /**
     * 打开Session
     * @access public
     */
    public function execute() {
        session_set_save_handler(array(&$this, "open"),
            array(&$this, "close"),
            array(&$this, "read"),
            array(&$this, "write"),
            array(&$this, "destroy"),
            array(&$this, "gc"));
    }
}
