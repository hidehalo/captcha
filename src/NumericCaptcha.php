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
    private function safeKey($key)
    {
       return self::DEFAULT_PREFIX . '.' . ($key?:self::DEFAULT_KEY);
    }


    /**
     * Get token
     * 
     * @codeCoverageIgnore
     * 
     * @param string $key
     * @return string
     */
    private function getToken($key)
    {
        return $this->cache->get($this->safeKey($key), '');
    }

    /**
     * Generate token
     *
     * @param integer $length
     * @param string $key
     * @return string
     */
    public function generate($length = 6, $key = null)
    {
        $token = $this->nano->formattedId('1234567890', $length);
        $ret = $this->cache->set($this->safeKey($key), $token, $this->timeout);
        // @codeCoverageIgnoreStart
        if (!$ret) {
            throw new \RuntimeException('Cache set failed.');
        }
        // @codeCoverageIgnoreEnd
        
        return $token;
    }
    
    /**
     * Verify token
     *
     * @param string $token
     * @param string $key
     * @return VerifyResult
     */
    public function verify($token, $key = null)
    {
        $ret = VerifyResult::VERIFY_FAILED;
        try {
            $realToken = $this->getToken($key);
            if ($realToken && $realToken == $token) {
                $ret = VerifyResult::VERIFY_OK;
            } elseif (!$realToken) {
                $ret = VerifyResult::VERIFY_EXPIRED;
            }
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $ret = VerifyResult::VERIFY_FAILED;
        } finally {
            // @codeCoverageIgnoreEnd
            return new VerifyResult($ret);
        }
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Destroy token
     *
     * @param string $key
     * @return bool
     */
    public function destroy($key = null)
    {
        return $this->cache->delete($this->safeKey($key));
    }
}
