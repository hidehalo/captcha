<?php

namespace Hidehalo\Captcha;

use Exception;
use Hidehalo\Captcha\VerifyResult;
use Psr\SimpleCache\CacheInterface;
use \Hidehalo\Nanoid\Client as NanoIdClient;

class NumericCaptcha
{
    const DEFAULT_TIMEOUT = 300;
    const DEFAULT_KEY = 'token';
    const DEFAULT_PREFIX = 'hidehalo.captcha.numeric_captcha';
    
    /**
     * @var unsigned_integer
     */
    private $timeout;

    /**
     * @var \Psr\SimpleCache\CacheInterface
     */

    protected $cache;

    /**
     * @var \Hidehalo\Nanoid\Client
     */
    protected $nano;

    /**
     * Constructor
     * 
     * @codeCoverageIgnore
     * @param \Psr\SimpleCache\CacheInterface $cache
     * @param integer $timeout
     */
    public function __construct(CacheInterface $cache, $timeout = self::DEFAULT_TIMEOUT)
    {
        $this->nano = new NanoIdClient();
        $this->cache = $cache;
        $this->timeout = is_numeric($timeout) && intval($timeout)>0? intval($timeout): self::DEFAULT_TIMEOUT;
    }

    /**
     * Generate safe key
     * 
     * @codeCoverageIgnore
     * 
     * @param string $key
     * @return string
     */
    private function safeKey()
    {
       return self::DEFAULT_PREFIX . '.' . self::DEFAULT_KEY;
    }


    /**
     * Get token
     * 
     * @codeCoverageIgnore
     * 
     * @param string
     * @return string
     */
    private function getToken()
    {
        return $this->cache->get($this->safeKey(), '');
    }

    /**
     * Generate token
     *
     * @param string $key
     * @param integer $length
     * @return string
     */
    public function generate($length = 6)
    {
        $token = '';
        $token = $this->nano->formattedId('1234567890', $length);
        $ret = $this->cache->set($this->safeKey(), $token, $this->timeout);
        if (!$ret) {
            $token = '';
        }
        
        return $token;
    }
    
    /**
     * Verify token
     *
     * @param string $token
     * @return VerifyResult
     */
    public function verify($token)
    {
        $ret = VerifyResult::VERIFY_FAILED;
        try {
            $realToken = $this->getToken();
            if ($realToken && $realToken == $token) {
                $ret = VerifyResult::VERIFY_OK;
                $this->cache->delete($this->safeKey());
            } elseif (!$realToken) {
                $ret = VerifyResult::VERIFY_EXPIRED;
            }
        } catch (Exception $e) {
            $ret = VerifyResult::VERIFY_FAILED;
        } finally {
            return new VerifyResult($ret);
        }
    }
}
