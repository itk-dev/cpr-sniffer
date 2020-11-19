<?php

declare(strict_types=1);

/*
 * This file is part of itk-dev/user-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\CprValidator;

/**
 * Class CprSniffer.
 *
 * Check a string for CPR resemblance.
 */
class CprValidator
{
    // Due to lacking capacity of valid modulo 11 numbers some years, there are
    // valid CPR numbers that don't pass modulo 11 check. In those cases we just
    // return true.
    // @See https://cpr.dk/cpr-systemet/personnumre-uden-kontrolciffer-modulus-11-kontrol/
    private array $noModuloCheckNumbers = [
        '010160',
        '010164',
        '010165',
        '010166',
        '010169',
        '010170',
        '010174',
        '010180',
        '010182',
        '010184',
        '010185',
        '010186',
        '010187',
        '010188',
        '010189',
        '010190',
        '010191',
        '010192',
    ];

    /**
     * Check if string contains valid CPR.
     *
     * @param string $cpr
     *   The string to check
     *
     * @return bool
     *   True if string contains a number that could be considered cpr
     */
    public function checkCpr(string $cpr): bool
    {
        if (empty($cpr)) {
            return false;
        }

        // Remove spaces and dashes to prepare for a simpler regex.
        // This way we check 3 formats 1234561234, 123456-1234 and 123456 1234.
        $stringConcatenated = str_replace(['-', ' '], '', $cpr);

        // Search the concatenated string for 10 digits in a row.
        $numberFound = preg_match('/(^|\D)\d{10}($|\D)/', $stringConcatenated, $result);

        if ($numberFound) {
            $number = $result[0];

            // Remove prefix from number.
            if (!empty($result[1])) {
                $number = substr($number, 1);
            }

            // Remove suffix from number.
            if (!empty($result[2])) {
                $number = substr($number, 0, -1);
            }

            // Prepare number for modulo 11 check.
            $arr = str_split($number);

            if ($this->mod11Chk($arr, $number) && $this->dateChk($number)) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Check against modulo 11.
     *
     * @param array  $array
     *   An array of characters
     * @param string $cpr
     *   A string that could be a CPR number
     *
     * @return bool
     *   True if the characters in union resemble a CPR number
     */
    private function mod11Chk(array $array, string $cpr): bool
    {
        if (\in_array(substr($cpr, 0, 6), $this->noModuloCheckNumbers)) {
            return true;
        }
        // Check each digit against it's weight.
        // Modulo 11 weights for CPR: 4, 3, 2, 7, 6, 5, 4, 3, 2, 1
        $value = 0;
        foreach ($array as $key => $v) {
            $v = intval($v);
            switch ($key) {
                case 6:
                case 0:
                    $value += $v * 4;
                    break;
                case 7:
                case 1:
                    $value += $v * 3;
                    break;
                case 8:
                case 2:
                    $value += $v * 2;
                    break;
                case 3:
                    $value += $v * 7;
                    break;
                case 4:
                    $value += $v * 6;
                    break;
                case 5:
                    $value += $v * 5;
                    break;
                case 9:
                    $value += $v * 1;
                    break;
            }
        }

        // Check the sum against modulo 11, if remainder is 0 the number resembles CPR.
        if (0 === $value % 11) {
            return true;
        }

        return false;
    }

    /**
     * Check against valid date.
     *
     * @param string $number
     *   A string that could be a CPR number
     *
     * @return bool return
     *   True if it's considered a date
     */
    private function dateChk(string $number): bool
    {
        $day = substr($number, 0, 2);
        $month = substr($number, 2, 2);
        $year = substr($number, 4, 2);

        if ($year < 21) {
            $prefix = '20';
        } else {
            $prefix = '19';
        }
        $year = $prefix.$year;

        return checkdate(intval($month), intval($day), intval($year));
    }
}
