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
     * Helper function to test private methods.
     *
     * @param $object
     *   The object to invoke method on
     * @param string $methodName
     *   The name of the method to invoke on the object
     * @param array $parameters
     *   The parameter to invoke the method on the object with
     *
     * @return mixed
     *   The result of the method
     *
     * @throws \ReflectionException
     */
    private function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Test that return type is bool.
     */
    public function testCheckCprReturnType()
    {
        $cprValidator = new CprValidator();
        $this->assertIsBool($cprValidator->checkCpr('010281-1234'));
    }

    /**
     * Test empty string gives false.
     */
    public function testCheckCprEmptyInput()
    {
        $cprValidator = new CprValidator();
        $this->assertFalse($cprValidator->checkCpr(''));
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

    /**
     * Test private ´dateChk´ method.
     */
    public function testPrivateDateChkMethod()
    {
        $cprValidator = new CprValidator();
        $this->assertTrue($this->invokeMethod($cprValidator, 'dateChk', ['19112020']));
        $this->assertFalse($this->invokeMethod($cprValidator, 'dateChk', ['35112020']));
    }

    /**
     * Test private 'mod11Chk' method.
     */
    public function testPrivateMod11ChkMethod()
    {
        $cprValidator = new CprValidator();
        $this->assertTrue($cprValidator->checkCpr('191120-4009'));
        $this->assertTrue($this->invokeMethod($cprValidator, 'mod11Chk', [str_split('1911204009'), '1911204009']));
    }
}
