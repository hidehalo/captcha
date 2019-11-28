<?php

namespace Hidehalo\Captcha;

class VerifyResult
{
    const VERIFY_OK = 0;
    const VERIFY_EXPIRED = 1;
    const VERIFY_FAILED = 2;

    /**
     * @var integer
     */
    protected $resultCode;

    //
    public function __construct($resultCode)
    {
        $this->resultCode = $resultCode;
    }


    /**
     * Test verify result successed
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->resultCode == self::VERIFY_OK;
    }

    /**
     * Test verify result expired
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->resultCode == self::VERIFY_EXPIRED;
    }

    /**
     * Test verify result failed
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->resultCode == self::VERIFY_FAILED;
    }
}
