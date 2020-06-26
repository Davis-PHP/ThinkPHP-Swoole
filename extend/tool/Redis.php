<?php


namespace tool;


class Redis
{
    protected $host;
    protected $password;
    protected $port;
    protected $select;
    protected $redis;

    public function __construct()
    {
//        $redisConf = config('redis');
//        $this->host = $redisConf['host'];
//        $this->password = $redisConf['password'];
//        $this->port = $redisConf['port'];
//        $this->select = $redisConf['select'];

        $this->host = '127.0.0.1';
        $this->password = '';
        $this->port = 6379;
        $this->select = 1;

        $this->redis = new \Redis();
        $this->redis->connect($this->host, $this->port, 5);
        $this->redis->auth($this->password);
        $this->redis->select($this->select);
    }

    /**
     * @param $key
     * @param $ttl
     * @param $value
     */
    public function set($key, $ttl, $value)
    {
        if (!empty($ttl) && $ttl > 0) {
            $res = $this->redis->setex($key, $ttl, $value);
        } else {
            $res = $this->redis->set($key, $value);
        }
        if (!$res) {
            throw new \Exception('Redis 写入失败!');
        }
        return $res;
    }

    /**
     * @param $key
     * @return bool|mixed|string
     */
    public function get($key)
    {
        $res = $this->redis->get($key);
        if (!$res) {
            return 'error';
        }
        return $res;
    }

    /**
     * @param $key
     * @return int|string
     */
    public function del($key)
    {
        $res = $this->redis->del($key);
        if (!$res) {
            return 'error';
        }
        return $res;
    }

    /**
     * @param $key
     * @param $value
     * @return bool|int|string
     */
    public function sadd($key, $value)
    {
        $res = $this->redis->sAdd($key, $value);
        if (!$res) {
            return 'error';
        }
        return $res;
    }

    /**
     * @param $key
     * @param $value
     * @return int|string
     */
    public function srem($key, $value)
    {
        $res = $this->redis->sRem($key, $value);
        if (!$res) {
            return 'error';
        }
        return $res;
    }

    /**
     * @param $key
     * @return array|string
     */
    public function sMembers($key)
    {
        $res = $this->redis->sMembers($key);
        if (!$res) {
            return 'error';
        }
        return $res;
    }
}