<?php

namespace ItkDev\Tests;

use ItkDev\CprValidator\CprValidator;
use PHPUnit\Framework\TestCase;

/**
 * Class CprValidatorTest.
 */
class CprValidatorTest extends TestCase
{
    /**
     * @dataProvider containsCprProvider
     */
    public function testContainsCpr(string $text, bool $expected)
    {
        $cprValidator = new CprValidator();
        $actual = $cprValidator->containsCpr($text);

        $this->assertSame($expected, $actual);
    }

    public function containsCprProvider(): array
    {
        return [
            [
                '',
                false,
            ],

            [
                '010160-1234',
                true,
            ],

            [
                '8010160-1234',
                false,
            ],

            [
                '1234567890',
                false,
            ],

            [
                '0101601234',
                true,
            ],

            [
                '120206 5513',
                false,
            ],

            [
                '120206 5518',
                true,
            ],

            [
                'My phone number is +4512345678 and my username is 0101608787.',
                true,
            ],

            [
                'The title with ISBN 1-888799-97-8 does not work. My username is 0101608787.',
                true,
            ],

            [
                'The title with ISBN 1-888799-97-8 does not work.

My username is 0101604242.',
                true,
            ],
        ];
    }

    /**
     * @dataProvider extractCprProvider
     */
    public function testExtractCpr(string $text, array $expected)
    {
        $cprValidator = new CprValidator();
        $actual = $cprValidator->extractCpr($text);

        $this->assertSame($expected, $actual);
    }

    public function extractCprProvider(): array
    {
        return [
            [
                '010160-1234',
                ['010160-1234'],
            ],

            [
                '1234567890',
                [],
            ],

            [
                '0101601234',
                ['0101601234'],
            ],

            [
                'My phone number is +4512345678 and my username is 0101608787.',
                ['0101608787'],
            ],

            [
                'The title with ISBN 1-888799-97-8 does not work. My username is 0101608787.',
                ['0101608787'],
            ],

            [
                'The title with ISBN 1-888799-97-8 does not work.

My username is 0101604242.',
                ['0101604242'],
            ],

            [
                'A list of valid cprs: 010160 1234, 010164 2345, 010165 3456
010166 4567
0101695678
0101706789
0101747890
010180-8901
010182-9012
010184-0123
010185-1234
231045-0637

This is not a cpr: 10101695678
',
                [
                    '010160 1234',
                    '010164 2345',
                    '010165 3456',
                    '010166 4567',
                    '0101695678',
                    '0101706789',
                    '0101747890',
                    '010180-8901',
                    '010182-9012',
                    '010184-0123',
                    '010185-1234',
                    '231045-0637',
                ],
            ],
        ];
    }

    /**
     * @dataProvider isCprProvider
     */
    public function testIsCpr(string $cpr, bool $expected)
    {
        $cprValidator = new CprValidator();
        $actual = $cprValidator->isCpr($cpr);

        $this->assertSame($expected, $actual);
    }

    public function isCprProvider(): array
    {
        return [
            [
                '0101601234',
                true,
            ],

            [
                '010160-1234',
                true,
            ],

            [
                '010160 1234',
                true,
            ],

            [
                '1234567890',
                false,
            ],

            [
                '0101601234',
                true,
            ],

            [
                '80101601234',
                false,
            ],

            [
                'horse',
                false,
            ],

            [
                '1-888799-97-8',
                false,
            ],
        ];
    }

    /**
     * Check that fake CPR returns false.
     */
    public function testCheckCprFakeCpr()
    {
        $cprValidator = new CprValidator();
        $this->assertFalse($cprValidator->checkCpr('010281-1234'));
        $this->assertFalse($cprValidator->checkCpr('0102811234'));
    }

    /**
     * Test that fake CPR returns true if in noModuloCheckNumbers array.
     */
    public function testCheckCprFakeCprM()
    {
        $cprValidator = new CprValidator();
        $this->assertTrue($cprValidator->checkCpr('010160-1234'));
        $this->assertTrue($cprValidator->checkCpr('0101601234'));
    }

    /**
     * Test that fake CPR returns true if in noModuloCheckNumbers array.
     */
    public function testCheckCprFakeCprModuloList()
    {
        $cprValidator = new CprValidator();
        $this->assertTrue($cprValidator->checkCpr('010160-1234'));
        $this->assertTrue($cprValidator->checkCpr('0101891234'));
    }
}
