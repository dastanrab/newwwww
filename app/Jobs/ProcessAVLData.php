<?php

namespace App\Jobs;

use App\Models\AVLData;
use App\Models\Car;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessAVLData implements ShouldQueue
{
    use Dispatchable,Queueable;

    public $data;
    public mixed $remoteAddress;

    public function __construct($data,$remoteAddress)
    {
        $this->data = $data;
        $this->remoteAddress=$remoteAddress;
    }
    public function onConnection()
    {
        return 'redis';
    }

    public function handle()
    {
        $this->data = base64_decode($this->data);
        $message = bin2hex($this->data);
         if (strlen($message) < 50) {
             $car = Car::where('is_active', true)->where('imei_hex', $message)->first();

             if ($car) {
                 $car->update(['ip' => $this->remoteAddress]);
             }

         } else {
             $car = Car::where('is_active', true)->where('ip', $this->remoteAddress)->first();

             if ($car) {
                 $data = $this->parseTeltonika($message);
                 for ($i=0; $i < $data['info']['avlCount']; $i++) {
                         AVLData::create([
                             'car_id' => $car->id,
                             'latitude' => $data[$i]['gps']['latitude'],
                             'longitude' => $data[$i]['gps']['longitude'],
                             'timestamp' => $data[$i]['date'],
                             'raw_data' => null,
                         ]);
                 }
             }
         }
    }
    function isAuthTeltonika(string $hex) {
        $firstByte = substr($hex, 0, 8);
        return hexdec($firstByte) !== 0;
    }

    function parseImeiTeltonika(string $hex) {
        $hexImei = substr($hex, 4);
        $imei = hex2bin($hexImei);
        $str = '';
        foreach (str_split(strrev((string)$imei)) as $i => $d) {
            $str .= $i % 2 !== 0 ? $d * 2 : $d;
        }
        if (array_sum(str_split($str)) % 10 === 0 && strlen($imei) == 15) return $imei;
        return false;
    }

    function parseTeltonika (string $text, &$success = null) {
        $response = ['info' => []];
        $response['info']['crc'] = substr($text, strlen($text)-8);
        $dl = hexdec(substr($text, 8, 8))*2;
        $avl = substr($text, 16, $dl);
        $pos = 0;
        $response['info']['codec'] = substr($avl, $pos, 2);
        $pos += 2;
        $success = $response['info']['avlCount'] = hexdec(substr($avl, $pos, 2));
        $pos += 2;
        $r = 0;
        for ($r = 0; $r < $response['info']['avlCount']; $r++) {
            $response[$r] = ['gps' => [], 'io' => [], 'ioAll' => [], 'ioAllHex' => []];
            /** timestamp: string|ISO Date Time */
            $response[$r]['dateHex'] = hexdec(substr($avl, $pos, 16));
            $response[$r]['date'] = date('Y-m-d H:i:s', (int)(hexdec(substr($avl, $pos, 16))/1000));
            $pos+=16;

            /** priority: integer  */
            $response[$r]['priority']= hexdec(substr($avl, $pos, 2));
            $pos+=2;

            /** longitude: float */
            $response[$r]['gps']['longitude'] = hexdec(substr($avl, $pos, 8))/10000000;
            $pos+=8;

            /** latitude: float */
            $response[$r]['gps']['latitude'] = hexdec(substr($avl, $pos, 8))/10000000;
            $pos+=8;

            /** altitude: integer|smallIntager */
            $response[$r]['gps']['altitude'] = hexdec(substr($avl, $pos, 4));
            $pos+=4;

            /** angle integer|smallIntager */
            $response[$r]['gps']['angle'] = hexdec(substr($avl, $pos, 4));
            $pos+=4;

            /** satellites integer|tynyIntager */
            $response[$r]['gps']['satellites'] = hexdec(substr($avl, $pos, 2));
            $pos+=2;

            /** speed: integer|smallIntager */
            $response[$r]['gps']['speed'] = hexdec(substr($avl, $pos, 4));
            $pos+=4;

            /** eventId: integer|tynyIntager - Event IO ID */
            $response[$r]['eventId'] = hexdec(substr($avl, $pos, 2));
            $pos+=2;

            /** ioCount integer|tynyIntager - count of elements */
            $response[$r]['ioCount'] = hexdec(substr($avl, $pos, 2));
            $pos+=2;

            /** oneBytesCount integer|tynyIntager - count of elements with one byte */
            $response[$r]['io']['oneBytesCount'] = hexdec(substr($avl, $pos, 2));
            $pos+=2;

            $response[$r]['io']['oneBytes'] = [];
            if ($response[$r]['io']['oneBytesCount']) {
                for ($bi = 0; $bi < $response[$r]['io']['oneBytesCount']; $bi++)
                {
                    $id = substr($avl, $pos, 2);
                    $did = hexdec($id);
                    $pos+=2;

                    $val = substr($avl, $pos, 2);
                    $dval = hexdec($val);
                    $pos+=2;
                    $response[$r]['io']['oneBytes'][$did] = $dval;
                    $response[$r]['ioAllHex'][$did] = $val;
                    $response[$r]['ioAll'][$did] = $dval;
                }
            }

            /** twoBytesCount integer|tynyIntager - count of elements with two byte */
            $response[$r]['io']['twoBytesCount'] = hexdec(substr($avl, $pos, 2));
            $pos+=2;

            $response[$r]['io']['twoBytes'] = [];
            if ($response[$r]['io']['twoBytesCount']) {
                for ($bi = 0; $bi < $response[$r]['io']['twoBytesCount']; $bi++)
                {
                    $id = substr($avl, $pos, 2);
                    $did = hexdec($id);
                    $pos+=2;

                    $val = substr($avl, $pos, 4);
                    $dval = hexdec($val);
                    $pos+=4;
                    $response[$r]['io']['twoBytes'][$did] = $dval;
                    $response[$r]['ioAllHex'][$did] = $val;
                    $response[$r]['ioAll'][$did] = $dval;
                }
            }

            /** fourBytesCount integer|tynyIntager - count of elements with four byte */
            $response[$r]['io']['fourBytesCount'] = hexdec(substr($avl, $pos, 2));
            $pos+=2;

            $response[$r]['io']['fourBytes'] = [];
            if ($response[$r]['io']['fourBytesCount']) {
                for ($bi = 0; $bi < $response[$r]['io']['fourBytesCount']; $bi++)
                {
                    $id = substr($avl, $pos, 2);
                    $did = hexdec($id);
                    $pos+=2;

                    $val = substr($avl, $pos, 8);
                    $dval = hexdec($val);
                    $pos+=8;
                    $response[$r]['io']['fourBytes'][$did] = $dval;
                    $response[$r]['ioAllHex'][$did] = $val;
                    $response[$r]['ioAll'][$did] = $dval;
                }
            }

            /** eightBytesCount integer|tynyIntager - count of elements with eight byte */
            $response[$r]['io']['eightBytesCount'] = hexdec(substr($avl, $pos, 2));
            $pos+=2;

            $response[$r]['io']['eightBytes'] = [];
            if ($response[$r]['io']['eightBytesCount']) {
                for ($bi = 0; $bi < $response[$r]['io']['eightBytesCount']; $bi++)
                {
                    $id = substr($avl, $pos, 2);
                    $did = hexdec($id);
                    $pos+=2;

                    $val = substr($avl, $pos, 16);
                    $dval = hexdec($val);
                    $pos+=16;
                    $response[$r]['io']['eightBytes'][$did] = $dval;
                    $response[$r]['ioAllHex'][$did] = $val;
                    $response[$r]['ioAll'][$did] = $dval;
                }
            }
        }
        return $response;
    }
}
