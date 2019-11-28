<?php

namespace Hidehalo\Captcha\Tests;

use PHPUnit\Framework\TestCase;
use Hidehalo\Captcha\VerifyResult;

class VerifyResultTest extends TestCase
{
    /**
     * @return void
     */
    public function testIsSuccessful()
    {
        $this->assertTrue((new VerifyResult(VerifyResult::VERIFY_OK))->isSuccessful());
    }

    /**
     * @return void
     */
    public function testIsExpired()
    {
        $this->assertTrue((new VerifyResult(VerifyResult::VERIFY_EXPIRED))->isExpired());
    }

    /**
     * @return void
     */
    public function testIsFailed()
    {
        $this->assertTrue((new VerifyResult(VerifyResult::VERIFY_FAILED))->isFailed());
    }
}
