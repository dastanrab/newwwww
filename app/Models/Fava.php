<?php

namespace App\Models;

use App\Classes\BaleService;
use App\Events\ActivityEvent;
use App\RecordsActivity;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use SoapClient;

class Fava extends Model
{
    use HasFactory;
    use RecordsActivity;

    public static function guzzle_http($url, $method, $parameters)
    {
        $client = new Client([
            'timeout' => 5,
            'verify' => '/etc/ssl/certs/ca-certificates.crt'
        ]);
        try {
            $response = $client->request('POST', $url, [
                'auth' => [env('FAVA_LOGIN'), env('FAVA_PASSWORD')], // Basic Auth
                'headers' => [
                    'soapAction' => $method,
                    'Content-Type' => 'text/xml',
                    'Connection' => 'close'
                ],
                'body' => $parameters,
            ]);

            $body = (string) $response->getBody();
        } catch (\Throwable $e) {
            dump($e->getMessage());
            return null;
        }
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();
        if ($statusCode >= 200 && $statusCode < 300) {
            // Successful response
            $xml = simplexml_load_string($body);
            $elements = $xml->xpath('/soap-env:Envelope/soap-env:Body/*/*/*/*');

            /*
            if (auth()->id() == developerId()) {
                dd($elements);
            }
            */
            $id = json_decode($elements[0], true);
            return $id;
        } else {
            if (auth()->id() == developerId()) {
                dump($body);
            }
            event(new ActivityEvent('عدم دریافت شناسه از فاوا', 'fava', false));
            return null;
        }


    }
    public static function curl_http($url, $method, $parameters)
    {
        $ch = curl_init();
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'soapAction: ' . $method,
            'Content-Type: text/xml',
            'Connection: close'
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, env('FAVA_LOGIN') . ':' . env('FAVA_PASSWORD')); // Basic auth
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_CAINFO, '/etc/ssl/certs/ca-certificates.crt');

        try {
            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (curl_errno($ch)) {
               dump(curl_error($ch));
                curl_close($ch);
                dd('stop');
            }
            curl_close($ch);

            if ($statusCode >= 200 && $statusCode < 300) {
                $xml = simplexml_load_string($response);
                $elements = $xml->xpath('/soap-env:Envelope/soap-env:Body/*/*/*/*');
                $id = json_decode($elements[0], true);
                return $id;
            } else {
                if (auth()->id() == developerId()) {
                    dump($parameters);
                    dump($response);
                }
                event(new ActivityEvent('عدم دریافت شناسه از فاوا', 'fava', false));
                return null;
            }

        } catch (\Throwable $e) {
            dump($e->getMessage());
            return null;
        }
    }

    public static function http($url, $method, $parameters)
    {
        $bale=new BaleService();
        try {
            $response = Http::timeout(5)->withBasicAuth(env('FAVA_LOGIN'), env('FAVA_PASSWORD'))
                ->withHeaders([
                    'soapAction' => $method
                ])
                ->withBody($parameters, 'text/xml')
                ->post($url);
        } catch (\Throwable $e) {
            if (auth()->id() == developerId())
            {
                dump($e->getMessage());
            }
            $bale->FavaError($method,$e->getMessage(),$parameters);
            event(new ActivityEvent($e->getMessage(), 'fava', false));
            return null;
        }
        if ($response->successful()) {
            $xml = simplexml_load_string($response->body());
            $elements = $xml->xpath('/soap-env:Envelope/soap-env:Body/*/*/*/*');
            /*if(auth()->id() == developerId()){
                dd($elements);
            }*/

            $id = json_decode($elements[0], true);
            return $id;
        }
        else {
            $bale->FavaError($method,substr($response->body(), 0, 200),$parameters);
            event(new ActivityEvent('عدم دریافت شناسه از فاوا', 'fava', false));
            return null;
        }
    }

    public static function http2($url, $method, $parameters)
    {
        try {
            $response = Http::timeout(5)->withBasicAuth(env('FAVA_LOGIN'), env('FAVA_PASSWORD'))
                ->withHeaders([
                    'soapAction' => $method
                ])
                ->withBody($parameters, 'text/xml')
                ->post($url);
        } catch (\Throwable $e) {
            event(new ActivityEvent($e->getMessage(), 'fava', false));
            return null;
        }
        dump($response);
        if ($response->successful()) {
            $xml = simplexml_load_string($response->body());
            $elements = $xml->xpath('/soap-env:Envelope/soap-env:Body/*/*/*/*');
            /*if(auth()->id() == developerId()){
                dd($elements);
            }*/
            if (auth()->id() == developerId()) {
                dump($response->body());
            }
            $id = json_decode($elements[0], true);
            return $id;
        }
        else {
            if (auth()->id() == developerId()) {
                dump('error',$response->body());
            }
            event(new ActivityEvent('عدم دریافت شناسه از فاوا', 'fava', false));
            return null;
        }
    }


    public static function createUser($data = [])
    {
        //todo add city_id 1
        if (App::environment('production') && $data['cityId'] && $data['cityId'] == 1) {
            $url = 'https://msb.mashhad.ir/IWMS/Database/Proxy/InsertCustomerService_db?wsdl';
            $GuildTypeRef = $data['guildId'] == 10 ? -1 : $data['guildId'];
            $fava_username = env('FAVA_LOGIN');
            $date = date('c');
            $parameters = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
            <soap:Envelope xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">
            <soap:Body>
            <GetIDService>
            <UserName>$fava_username</UserName>
            <CustomerId>{$data['userId']}</CustomerId>
            <IsLegal>{$data['isLegal']}</IsLegal>
            <FirstName>{$data['name']}</FirstName>
            <LastName>{$data['lastname']}</LastName>
            <GuildTypeRef>$GuildTypeRef</GuildTypeRef>
            <GuildTitle>{$data['guildTitle']}</GuildTitle>
            <Mobile>{$data['mobile']}</Mobile>
            <RegDate>$date</RegDate>
            <NationalCode></NationalCode>
            <Lat></Lat>
            <Long></Long>
            <Address></Address>
            <BirthDate></BirthDate>
            <IsMale></IsMale>
            <Region></Region>
            <District></District>
            <NationalCodeLegal></NationalCodeLegal>
            </GetIDService>
            </soap:Body>
            </soap:Envelope>";
            return self::http($url, 'InsertCustomerService', $parameters);
        }
    }

    public static function InsertCar($data = [])
    {
        if (App::environment('production') && $data['cityId']) {
            $url = 'https://msb.mashhad.ir/IWMS/Database/Proxy/InsertCarService_db?wsdl';
            $StartDate = now()->format('Y-m-d H:i:s');
            $type_id = request()->type_id;
            $fava_username = env('FAVA_LOGIN');

            $parameters = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
            <soap:Envelope xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">
            <soap:Body>
            <GetIDService>
            <UserName>$fava_username</UserName>
            <Plaque>{$data['plaque']}</Plaque>
            <CarTypeRef>{$data['typeId']}</CarTypeRef>
            <DriverMobile>{$data['mobile']}</DriverMobile>
            <StartDate>$StartDate</StartDate>
            <EndDate></EndDate>
            </GetIDService>
            </soap:Body>
            </soap:Envelope>";
            return self::http($url,'InsertCarService',$parameters);
        }
    }
    public static function updateRequest($fava_id, $status, $reason=null)
    {
        if (App::environment('production')) {
            $url = 'https://msb.mashhad.ir/IWMS/Database/Proxy/UpdateRequestService_db?wsdl';

            $fava_username = env('FAVA_LOGIN');
            $parameters = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
            <soap:Envelope xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">
            <soap:Body>
            <GetIDService>
            <UserName>$fava_username</UserName>
            <RequestRef>$fava_id</RequestRef>
            <StatusRef>$status</StatusRef>
            <DriverCancellationReasonRef>$reason</DriverCancellationReasonRef>
            <CarRef></CarRef>
            </GetIDService>
            </soap:Body>
            </soap:Envelope>";

            return self::http($url,'UpdateRequestService',$parameters);
        }
    }

    public static function insertRequest($data = [])
    {
        if (App::environment('production') && $data['cityId'] == 1) {
            $url = 'https://msb.mashhad.ir/IWMS/Database/Proxy/InsertRequestService_db?wsdl';

            $RequestDate = new Carbon($data['createdAt']);
            $RequestDate = $RequestDate->toIso8601String();
            $DeadlineFromDateTime = new Carbon($data['startDeadline']);
            $DeadlineFromDateTime = $DeadlineFromDateTime->toIso8601String();
            $DeadlineToDateTime = new Carbon($data['endDeadline']);
            $DeadlineToDateTime = $DeadlineToDateTime->toIso8601String();

            $fava_username = env('FAVA_LOGIN');
            $parameters = '<?xml version="1.0" encoding="utf-8"?>
                <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                <GetIDService>
                <UserName>'.$fava_username.'</UserName>
                <RequestId>'.$data['submitId'].'</RequestId>
                <CustomerRef>'.$data['userFavaId'].'</CustomerRef>
                <RequetDate>'.$RequestDate.'</RequetDate>
                <DeadlineFromDateTime>'.$DeadlineFromDateTime.'</DeadlineFromDateTime>
                <DeadlineToDateTime>'.$DeadlineToDateTime.'</DeadlineToDateTime>
                <StatusRef>1</StatusRef>
                <DriverCancellationReasonRef></DriverCancellationReasonRef>
                <Lat>'.$data['lat'].'</Lat>
                <Long>'.$data['lon'].'</Long>
                <CarRef></CarRef>
                <Address>'.$data['address'].'</Address>
                <Region>'.$data['region'].'</Region>
                <District>'.$data['district'].'</District>
                <DetailJson>'.$data['recyclables'].'</DetailJson>
                </GetIDService>
                </soap:Body>
                </soap:Envelope>';
            return self::http($url,'InsertRequestService',$parameters);
        }
    }

    public static function InsertCollectingService($submit,$receives)
    {
        if (App::environment('production') && $submit->city_id == 1) {
            $url = 'https://msb.mashhad.ir/IWMS/Database/Proxy/InsertCollectingService_db?wsdl';

            $date = date('c');
            $receives = json_encode($receives);
            $fava_username = env('FAVA_LOGIN');
            $parameters = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
            <GetIDService>
            <UserName>' . $fava_username . '</UserName>
            <CollectingId>' . $submit->driver->id . '</CollectingId>
            <RequestRef>' . $submit->fava_id . '</RequestRef>
            <CustomerRef>' . $submit->user->fava_id . '</CustomerRef>
            <CollectingDate>' . $date . '</CollectingDate>
            <Lat>' . $submit->address->lon . '</Lat>
            <Long>' . $submit->address->lat . '</Long>
            <CarRef>' . $submit->driver->user->cars->where('is_active', true)->first()->fava_id . '</CarRef>
            <Address>' . $submit->address->address . '</Address>
            <DriverScore></DriverScore>
            <DriverScoreDesc></DriverScoreDesc>
            <CustomerScore></CustomerScore>
            <CustomerScoreDesc></CustomerScoreDesc>
            <Region>' . $submit->address->region . '</Region>
            <District>' . $submit->address->district . '</District>
            <DetailJson>' . $receives . '</DetailJson>
            </GetIDService>
            </soap:Body>
            </soap:Envelope>';
            return self::http($url, 'InsertCollectingService', $parameters);
        }
    }

    public static function InsertPaymentService($submit,$bankTrackingCode)
    {
        if (App::environment('production') && $submit->city_id == 1) {
            $url = 'https://msb.mashhad.ir/IWMS/Database/Proxy/InsertPaymentService_db?wsdl';
            $PaymentDate = date('c');
            $fava_username = env('FAVA_LOGIN');
            $parameters = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
            <GetIDService>
            <UserName>' . $fava_username . '</UserName>
            <IsPayToMunicipality>0</IsPayToMunicipality>
            <CustomerRef>' . $submit->user->fava_id . '</CustomerRef>
            <PaymentTypeRef>4</PaymentTypeRef>
            <BankTrackingCode>' . $bankTrackingCode . '</BankTrackingCode>
            <PaymentDate>' . $PaymentDate . '</PaymentDate>
            <Amount>' . $submit->total_amount * 10 . '</Amount>
            <PaymentId>' . $submit->driver->id . '</PaymentId>
            </GetIDService>
            </soap:Body>
            </soap:Envelope>';
            return self::http($url, 'InsertPaymentService', $parameters);
        }
    }

    public static function InsertPaymentServiceToUser($user,$amount,$paymentId,$paymentDate,$bankTrackingCode)
    {
        if (App::environment('production') && $user->city_id == 1) {
            $url = 'https://msb.mashhad.ir/IWMS/Database/Proxy/InsertPaymentService_db?wsdl';
            $fava_username = env('FAVA_LOGIN');
            $amount = (int)$amount * 10;
            $parameters = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
            <GetIDService>
            <UserName>' . $fava_username . '</UserName>
            <IsPayToMunicipality>0</IsPayToMunicipality>
            <CustomerRef>' . $user->fava_id . '</CustomerRef>
            <PaymentTypeRef>4</PaymentTypeRef>
            <BankTrackingCode>' . $bankTrackingCode . '</BankTrackingCode>
            <PaymentDate>' . $paymentDate . '</PaymentDate>
            <Amount>' . $amount . '</Amount>
            <PaymentId>'.$paymentId.'</PaymentId>
            </GetIDService>
            </soap:Body>
            </soap:Envelope>';
            return self::http2($url, 'InsertPaymentService', $parameters);
        }
    }

    public static function CurrentPricesService( $args = [])
    {
        $url = 'https://msb.mashhad.ir/IWMS/Database/Proxy/CurrentPricesService_db?wsdl';
        try {
            $client = new SoapClient($url, [
                'login' => env('FAVA_LOGIN'),
                'password' => env('FAVA_PASSWORD'),
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
                'cache_wsdl' => WSDL_CACHE_BOTH
            ]);
            return $client->__soapCall('CurrentPricesServiceSelect', $args);
        } catch (Exeption $e) {
            event(new ActivityEvent($e->getMessage(), 'fava', false));
        }
    }

    public static function getIdService($type,$key)
    {
        $url = 'https://msb.mashhad.ir/IWMS/Database/Proxy/GetIDService_db?wsdl';
        $fava_username = env('FAVA_LOGIN');
        $parameters = <<<XML
        <?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
        <GetIDService>
        <UserName>$fava_username</UserName>
        <ObjectType>$type</ObjectType>
        <ObjectKey>$key</ObjectKey>
        </GetIDService>
        </soap:Body>
        </soap:Envelope>
        XML;
        return self::http($url, 'GetIDService', $parameters);
    }

    public static function GetUserIDService($mobile)
    {
        return self::getIdService(2,$mobile);
    }

    public static function insertCustomerService($user)
    {
        if($user->city_id == 1 && env('APP_ENV') == 'production'):
            $url = 'https://msb.mashhad.ir/IWMS/Database/Proxy/InsertCustomerService_db?wsdl';
            $fava_username = env('FAVA_LOGIN');
            $RegDate = new Carbon($user->created_at);
            $RegDate = $RegDate->toIso8601String();
            if ($user->legal == 0) {
                $name = $user->name ?? 'null';
                $lastname = $user->lastname ?? 'null';
                $guild_id = null;
                $guild_title = null;
            } else {
                $name = 'null';
                $lastname = 'null';
                $guild_id = $user->guild_id ?? 10;
                $guild_title = $user->guild_title ?? 'null';
            }
            $GuildTypeRef = $guild_id == 10 ? -1 : $guild_id;
            if(auth()->id() == 67775) {
                dump(
                    ' UserName => '.$fava_username,
                ' CustomerId => '.$user->id.
                '  IsLegal => '.$user->legal.
                'FirstName => '.$name.
                ' LastName => '.$lastname.
                ' GuildTypeRef => '.$GuildTypeRef.
                'GuildTitle => '.$guild_title.
                ' Mobile => '.$user->mobile.
                ' RegDate => '.$RegDate.
                'NationalCode =>'.
                ' Lat => '.
                'Long =>'.
                ' Address => '.
                ' BirthDate => '.
                ' IsMale => '.
                ' Region => '.
                ' District =>'.
                ' NationalCodeLegal => '
                );
            }
            $parameters = <<<XML
                <?xml version="1.0" encoding="utf-8"?>
                <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                <GetIDService>
                <UserName>$fava_username</UserName>
                <CustomerId>$user->id</CustomerId>
                <IsLegal>$user->legal</IsLegal>
                <FirstName>$name</FirstName>
                <LastName>$lastname</LastName>
                <GuildTypeRef>$GuildTypeRef</GuildTypeRef>
                <GuildTitle>$guild_title</GuildTitle>
                <Mobile>$user->mobile</Mobile>
                <RegDate>$RegDate</RegDate>
                <NationalCode></NationalCode>
                <Lat></Lat>
                <Long></Long>
                <Address></Address>
                <BirthDate></BirthDate>
                <IsMale></IsMale>
                <Region></Region>
                <District></District>
                <NationalCodeLegal></NationalCodeLegal>
                </GetIDService>
                </soap:Body>
                </soap:Envelope>
                XML;
            if(auth()->id() == 0){ // 57165
                $method = 'InsertCustomerService';
                try {
                    $response = Http::timeout(5)->withBasicAuth(env('FAVA_LOGIN'), env('FAVA_PASSWORD'))
                        ->withHeaders([
                            'soapAction' => $method
                        ])
                        ->withBody($parameters, 'text/xml')
                        ->post($url);
                    dd($response->body());
                } catch (\Throwable $e) {
                    event(new ActivityEvent($e->getMessage(), 'fava', false));
                    return null;
                }
            }
            else{
                return self::http($url, 'InsertCustomerService', $parameters);
            }

        endif;
    }

    public static function GetCarIDService($car)
    {
        return self::getIdService(1,$car->id);
    }

    public static function InsertCarService($car)
    {
        if($car->user->city_id == 1 && env('APP_ENV') == 'production'):
            $url = 'https://msb.mashhad.ir/IWMS/Database/Proxy/InsertCarService_db?wsdl';
            $plaque = $car->plaque;
            $type_id = $car->type_id;
            $DriverMobile = $car->user->mobile;
            $StartDate = new Carbon($car->created_at);
            $StartDate = $StartDate->toIso8601String();
            $fava_username = env('FAVA_LOGIN');
            $parameters = <<<XML
            <?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
            <GetIDService>
            <UserName>$fava_username</UserName>
            <Plaque>$plaque</Plaque>
            <CarTypeRef>$type_id</CarTypeRef>
            <DriverMobile>$DriverMobile</DriverMobile>
            <StartDate>$StartDate</StartDate>
            <EndDate></EndDate>
            </GetIDService>
            </soap:Body>
            </soap:Envelope>
            XML;
            return self::http($url, 'InsertCarService', $parameters);
        endif;
    }

    public static function GetSubmitIDService($submit)
    {
        return self::getIdService(3,$submit->id);
    }

    public static function insertRequestService($submit)
    {
        if($submit->city_id == 1 AND env('APP_ENV') == 'production'):
            $url = 'https://msb.mashhad.ir/IWMS/Database/Proxy/InsertRequestService_db?wsdl';
            $address = Address::withTrashed()->find($submit->address_id);

            $RequestDate = new Carbon($submit->created_at);
            $RequestDate = $RequestDate->toIso8601String();
            $DeadlineFromDateTime = new Carbon($submit->start_deadline);
            $DeadlineFromDateTime = $DeadlineFromDateTime->toIso8601String();
            $DeadlineToDateTime = new Carbon($submit->end_deadline);
            $DeadlineToDateTime = $DeadlineToDateTime->toIso8601String();
            $DriverCancellationReasonRef = '';
            $CarRef = null;

            $user = User::find($submit->user_id);

            $fava_username = env('FAVA_LOGIN');
            $parameters = <<<XML
                <?xml version="1.0" encoding="utf-8"?>
                <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                <GetIDService>
                <UserName>$fava_username</UserName>
                <RequestId>$submit->id</RequestId>
                <CustomerRef>$user->fava_id</CustomerRef>
                <RequetDate>$RequestDate</RequetDate>
                <DeadlineFromDateTime>$DeadlineFromDateTime</DeadlineFromDateTime>
                <DeadlineToDateTime>$DeadlineToDateTime</DeadlineToDateTime>
                <StatusRef>1</StatusRef>
                <DriverCancellationReasonRef>$DriverCancellationReasonRef</DriverCancellationReasonRef>
                <Lat>$address->lat</Lat>
                <Long>$address->lon</Long>
                <CarRef>$CarRef</CarRef>
                <Address>$address->address</Address>
                <Region>$address->region</Region>
                <District>$address->district</District>
                <DetailJson>$submit->recyclables</DetailJson>
                </GetIDService>
                </soap:Body>
                </soap:Envelope>
                XML;

            if(auth()->id() == 1){ // 57165
                dump($address);
                $method = 'InsertRequestService';
                try {
                    $response = Http::timeout(5)->withBasicAuth(env('FAVA_LOGIN'), env('FAVA_PASSWORD'))
                        ->withHeaders([
                            'soapAction' => $method
                        ])
                        ->withBody($parameters, 'text/xml')
                        ->post($url);
                    dd($response->body());
                } catch (\Throwable $e) {
                    event(new ActivityEvent($e->getMessage(), 'fava', false));
                    return null;
                }
            }
            else {
                if (auth()->id() == developerId())
                {
                    return self::curl_http($url,'InsertRequestService', $parameters);
                }
                else{
                    return self::http($url, 'InsertRequestService', $parameters);
                }
            }
        endif;

    }

    public static function GetDriverIDService($driver)
    {
        return self::getIdService(4,$driver->id);
    }

    public static function insertCollectingService2($driver)
    {
        $submit = Submit::with(['address' => function ($query) {
            $query->withTrashed();
        }])->find($driver->submit_id);
        dump($submit->toArray());
        if($submit->city_id == 1 AND env('APP_ENV') == 'production'):
            $CollectingDate = new Carbon($driver->collected_at);
            $CollectingDate = $CollectingDate->toIso8601String();
            $user=User::query()->where('id','=',$submit->user_id)->first();
            if ($user)
            {
                dump($user);
            }
            $receives = [];
            foreach ($driver->receives as $key => $receive) {
                array_push($receives, ['GoodRef' => $receive->fava_id, 'Quantity' => $receive->weight, 'Price' => $receive->price * 10]);
            }
            $receives = json_encode($receives);
            $fava_username = env('FAVA_LOGIN');
            $url = 'https://msb.mashhad.ir/IWMS/Database/Proxy/InsertCollectingService_db?wsdl';
            if(auth()->id() == developerId() ) {
                dump(
                    ' CollectingId: ' . $driver->id.
                    ' SubmitId: ' . $driver->submit_id.
                    ' RequestRef: ' . $submit->fava_id.
                    ' CustomerRef: ' . $submit->user->fava_id.
                    ' CollectingDate: ' . $CollectingDate.
                    ' Lat: ' . $submit->address->lat.
                    ' Long: ' . $submit->address->lon.
                    ' CarRef: ' . $driver->user->cars[0]->fava_id.
                    ' Address: ' . $submit->address->address.
                    ' Region: ' . $submit->address->region.
                    ' District: ' . $submit->address->district.
                    ' DetailJson: ' . $receives
                );
            }
            $parameters = '<?xml version="1.0" encoding="utf-8"?>
                <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                <GetIDService>
                <UserName>'.$fava_username.'</UserName>
                <CollectingId>'.$driver->id.'</CollectingId>
                <RequestRef>'.$submit->fava_id.'</RequestRef>
                <CustomerRef>'.$submit->user->fava_id.'</CustomerRef>
                <CollectingDate>'.$CollectingDate.'</CollectingDate>
                <Lat>'.$submit->address->lat.'</Lat>
                <Long>'.$submit->address->lon.'</Long>
                <CarRef>'.$driver->user->cars[0]->fava_id.'</CarRef>
                <Address>'.$submit->address->address.'</Address>
                <DriverScore></DriverScore>
                <DriverScoreDesc></DriverScoreDesc>
                <CustomerScore></CustomerScore>
                <CustomerScoreDesc></CustomerScoreDesc>
                <Region>'.$submit->address->region.'</Region>
                <District>'.$submit->address->district.'</District>
                <DetailJson>'.$receives.'</DetailJson>
                </GetIDService>
                </soap:Body>
                </soap:Envelope>';

            if(auth()->id() == 57165 ){ // 57165
                $method = 'InsertCollectingService';
                try {
                    $response = Http::timeout(5)->withBasicAuth(env('FAVA_LOGIN'), env('FAVA_PASSWORD'))
                        ->withHeaders([
                            'soapAction' => $method
                        ])
                        ->withBody($parameters, 'text/xml')
                        ->post($url);
                    dump($response->body());
                } catch (\Throwable $e) {
                    if (auth()->id() == developerId() )
                    {
                        dump($e->getMessage());
                    }
                    event(new ActivityEvent($e->getMessage(), 'fava', false));
                    return null;
                }
            }
            else {
                return self::http($url, 'InsertCollectingService', $parameters);
            }
        endif;

    }

    public static function GetPaymentIDService($id)
    {
        return self::getIdService(5,$id);
    }

    public static function surveyLink($customerRef,$mobile)
    {
        $token = '844658EA-FD90-4A09-8975-7E01E4CF9E95';
        $date = date('Ymd');
        $hashInput = $token . $mobile . $date;
        $hash = strtoupper(hash('md5', $hashInput));
        return "https://iwms.mashhad.ir/#/iwcs-survey?cid=$customerRef&hash=$hash";
    }


}
