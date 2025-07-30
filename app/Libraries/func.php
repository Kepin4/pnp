<?php

namespace App\Libraries;

use App\Models\Base_model;

class func
{
    protected $db;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    function helper($x)
    {
        if (empty($x)) {
            return "";
        }
        global $mysqli;
        return mysqli_real_escape_string($mysqli, $x);
    }

    public static function getKey($namatable, $namafieldnotrans, $kodedepan, $tanggal, $pakaitahun = false, $pakaibulan = false, $myjumlah0 = 3)
    {
        $baseCode = $kodedepan;
        if ($pakaitahun) {
            $baseCode .= $tanggal->format('y');
        }

        if ($pakaibulan) {
            $baseCode .= $tanggal->format('m');
        }

        $transactionCount = self::getTransactionCount($namatable, $namafieldnotrans, $baseCode);
        $newTransactionCode = self::generateTransactionCode($baseCode, $transactionCount, $myjumlah0);

        while (self::transactionExists($namatable, $namafieldnotrans, $newTransactionCode)) {
            $transactionCount += 1;
            $newTransactionCode = self::generateTransactionCode($baseCode, $transactionCount, $myjumlah0);
        }

        return $newTransactionCode;
    }

    public static function getTransactionCount($namatable, $namafieldnotrans, $baseCode)
    {
        $qry = new Base_model();
        $sel = "SELECT COUNT(*) AS hit FROM $namatable WHERE $namafieldnotrans LIKE '" . $baseCode . "%'";
        $dvlaporKode = $qry->use($sel);
        return (int) $dvlaporKode[0]->hit;
    }

    public static function generateTransactionCode($baseCode, $transactionCount, $myjumlah0)
    {
        $paddedNumber = str_pad($transactionCount + 1, $myjumlah0, '0', STR_PAD_LEFT);
        return $baseCode . $paddedNumber;
    }

    public static function transactionExists($namatable, $namafieldnotrans, $transactionCode)
    {
        $qry = new Base_model();
        $cekdob = "SELECT $namafieldnotrans FROM $namatable WHERE $namafieldnotrans = '" . helper($transactionCode) . "'";
        $dvlaporKode = $qry->use($cekdob);
        return count($dvlaporKode) > 0;
    }

    public static function NumNULL($xNumber = NULL)
    {
        if (empty($xNumber)) {
            return 0;
        }
        return $xNumber;
    }

    public static function print_ar($dt)
    {
        echo '<pre>';
        print_r($dt);
        echo '</pre>';
    }
}
