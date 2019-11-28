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
        $token = $captcha->generate();
        $result1 = $captcha->verify($token);
        $this->assertTrue($result1->isSuccessful());

        $result2 = $captcha->verify($token);
        $this->assertTrue($result2->isExpired());
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
