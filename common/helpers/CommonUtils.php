<?php
/**
 *
 * @author Nguyen Chi Thuc
 */

namespace common\helpers;

use common\models\UserRegistration;
use Yii;
use yii\helpers\VarDumper;

class CommonUtils
{

    const TELCO_MOBIFONE = 0;
    const TELCO_VINAPHONE = 2;
    const TELCO_VIETTEL = 3;
    const TELCO_OTHER = 4;

    public static $telcos = [
        self::TELCO_MOBIFONE => 'Mobifone',
        self::TELCO_VINAPHONE => 'Vinaphone',
        self::TELCO_VIETTEL => 'Viettel',
        self::TELCO_OTHER => 'Other',
    ];

    public static function pre($content)
    {
        echo '<pre>';
        print_r($content);
        echo '</pre>';
        die;
    }

    public static function rrmdir($path)
    {
        $path = rtrim($path, '/') . '/';

        // Remove all child files and directories.
        $items = glob($path . '*');

        foreach ($items as $item) {
            is_dir($item) ? self::rrmdir($item) : unlink($item);
        }

        // Remove directory.
        rmdir($path);
    }

    public static function getListParent($item, &$result = [])
    {
        if ($item->parent === null) {
            return $result;
        } else {
            if (!in_array($item->parent->id, $result)) {
                $result[] = $item->parent->id;
                CommonUtils::getListParent($item->parent, $result);
            }
            return $result;
        }
    }

    public static function columnLabel($value, $data)
    {
        if (array_key_exists($value, $data)) {
            return $data[$value];
        }
        return $value;
    }

    public static function displayDate($ts, $format = "d/m/Y")
    {
        if (!$ts) return '';
        $date = new \DateTime("@$ts");
        return $date->format($format);
    }

    public static function displayDateTime($ts, $format = "d/m/Y , H:i:s")
    {
        if (!$ts) return '';
        $date = new \DateTime("@$ts");
        return $date->format($format);
    }

    public static function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    public static function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }


    public static function replaceParam($message, $params, $replace)
    {
        if (is_array($params)) {
            if (is_array($replace)) {
                $cnt = count($params);
                for ($i = 0; $i < $cnt; $i++) {
                    $message = str_replace('{' . $params[$i] . '}', $replace[$i], $message);
                }
            }
        }

        return $message;

    }

    public static function getClientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENTIP'])) {
            return $_SERVER['HTTP_CLIENTIP'];
        }

        if (!empty($_SERVER['X_REAL_ADDR'])) {
            return $_SERVER['X_REAL_ADDR'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(':', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return $ips[0];
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return gethostbyname(gethostname()); // tra ve ip local khi chay CLI
    }

    public static function formatNumber($number)
    {
        $formatter = new \yii\i18n\Formatter();
        $formatter->thousandSeparator = ',';
        $formatter->decimalSeparator = '.';
//       return number_format($number, 2, '.', ',');
        return $formatter->asInteger($number);
    }

    public static function getTimeStamp($format = "d/m/Y , H:i:s", $dateTime)
    {
        $date_obj = \DateTime::createFromFormat($format, $dateTime);
        return $date_obj->getTimestamp();
    }

    /**
     *
     * @param string $mobileNumber
     * @param int $typeFormat format: 0: format 84xxx, 1: format 0xxxx, 2: format xxxx
     * @return String valid mobile
     */
    public static function validateMobile($mobileNumber, $typeFormat = 0)
    {
        $valid_number = '';
        // Remove string "+"
        $mobileNumber = str_replace('+84', '84', $mobileNumber);

        if (preg_match('/^(84|0|)(\d{9}|\d{10})$/', $mobileNumber, $matches)) {
            /**
             * $typeFormat == 0: 8491xxxxxx
             * $typeFormat == 1: 091xxxxxx
             * $typeFormat == 2: 91xxxxxx
             */
            if ($typeFormat == 0) {
                if ($matches[1] == '0' || $matches[1] == '') {
                    $valid_number = preg_replace('/^(0|)/', '84', $mobileNumber);
                } else {
                    $valid_number = $mobileNumber;
                }
            } else if ($typeFormat == 1) {
                if ($matches[1] == '84' || $matches[1] == '') {
                    $valid_number = preg_replace('/^(84|)/', '0', $mobileNumber);
                } else {
                    $valid_number = $mobileNumber;
                }
            } else if ($typeFormat == 2) {
                if ($matches[1] == '84' || $matches[1] == '0') {
                    $valid_number = preg_replace('/^(84|0)/', '', $mobileNumber);
                } else {
                    $valid_number = $mobileNumber;
                }
            }
        }
        return $valid_number;
    }


    /**
     * @param $mobileNumber
     * @param int $typeFormat
     * $typeFormat == 0: 8491xxxxxx
     * $typeFormat == 1: 091xxxxxx
     * $typeFormat == 2: 91xxxxxx
     * @return int|string
     */
    public static function validateTelco($mobileNumber, $typeFormat = 0)
    {
        // Remove string "+"
        $mobileNumber = str_replace('+84', '84', $mobileNumber);

        if (preg_match('/^(84|0|)(91|94|123|124|125|127|129)\d{7}$/', $mobileNumber, $matches)) {
            $telco = self::TELCO_VINAPHONE;
        } else
            if (preg_match('/^(84|0|)(96|97|98|162|163|164|165|166|167|168|169)\d{7}$/', $mobileNumber, $matches)) {
                $telco = self::TELCO_VIETTEL;
            } else
                if (preg_match('/^(84|0|)(90|93|120|121|122|126|128|89)\d{7}$/', $mobileNumber, $matches)) {
                    $telco = self::TELCO_MOBIFONE;
                } else {
                    $telco = self::TELCO_OTHER;
                }
        return $telco;
    }

    public static function mix_array($array)
    {
        $m = count($array);
        // Chừng nào vẫn còn phần tử chưa được xáo trộn thì vẫn tiếp tục
        while ($m) {
            $m--;
            // Lấy ra 1 phần tử
            $i = rand(0, $m);
            // Sau đó xáo trộn nó
            $t = $array[$m];
            $array[$m] = $array[$i];
            $array[$i] = $t;
        }
        return $array;
    }
}
