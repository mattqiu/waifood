<?php
namespace Common\Model;
use Think\Cache\Driver\Redis;
use Think\Log;
use Think\Model;

class RedisModel extends Model
{
    const DATA_CACHE_TIME = 86400;
    const DATA_CACHE_PREFIX = 'WAIFOOD:';

    /**
     * 设置缓存
     * @param $k
     * @param $v
     */
    public static function  set($k,$v,$time = self::DATA_CACHE_TIME){
        $redis =new \Redis();
        $redis->connect('127.0.0.1',6379);
        $key = self::DATA_CACHE_PREFIX.$k;
        $redis->set($key,$v,$time);
    }

    public static function  get($k){
        $redis =new \Redis();
        $redis->connect('127.0.0.1',6379);
        $key = self::DATA_CACHE_PREFIX.$k;
        return  $redis->get($key);
    }

    /**
     * 删除单个key
     * @param $k
     */
    public static function del($k){
        $redis =new \Redis();
        $redis->connect('127.0.0.1',6379);
        $key = self::DATA_CACHE_PREFIX.$k;
        $keys = $redis->keys($key);
        if(!empty($keys)) {
            $redis->del($k);
        }
    }

    /**
     * 删除一个key 组
     * @param $k
     */
    public static function delBykeys($k){
        $prefix = self::DATA_CACHE_PREFIX.$k;
        $menuKey = $prefix."*";
        self::deleteVagueKeys($menuKey);
    }

    /**
     *  删除通配类这类的模糊key
     *  如果key过多，容易出现性能问题
     */
    private static function deleteVagueKeys($key){
        $redis =new \Redis();
        $redis->connect('127.0.0.1',6379);
        Log::write("delete key $key",Log::INFO);
        $keys = $redis->keys($key);
        if(!empty($keys)){
            foreach($keys as $k){
                $redis->del($k);
                Log::record("delete cache key".$k,Log::INFO);
            }
        }
    }

}