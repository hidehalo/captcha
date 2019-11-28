<?php

namespace Hidehalo\Captcha\Tests;

use PHPUnit\Framework\TestCase;
use Hidehalo\Captcha\NumericCaptcha;
use Symfony\Component\Cache\Simple\ArrayCache;

class NumericCaptchaTest extends TestCase
{
    /**
     * @dataProvider captchaProvider
     *
     * @return void
     */
    public function testGenerateToken(NumericCaptcha $captcha)
    {
        $testLen = mt_rand(6, 64);
        $token = $captcha->generate($testLen);
        $this->assertEquals($testLen, strlen($token));
    }

    /**
     * @dataProvider captchaProvider
     *
     * @return void
     */
    public function testVerify(NumericCaptcha $captcha)
    {
        $testLen = mt_rand(6, 64);
        $token = $captcha->generate($testLen, "TEST_KEY");
        $result1 = $captcha->verify("ERR_TOKEN", "TEST_KEY");
        $this->assertTrue($result1->isFailed());

        $result2 = $captcha->verify($token, "TEST_KEY");
        $this->assertTrue($result2->isSuccessful());
        
        $result3 = $captcha->verify($token);
        $this->assertTrue($result3->isExpired());
    }

    //
    public function captchaProvider()
    {
        return [
            [
                new NumericCaptcha(new ArrayCache())
            ]
        ];
    }
}
