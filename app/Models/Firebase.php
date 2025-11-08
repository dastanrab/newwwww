<?php

namespace App\Models;

use App\Events\ActivityEvent;
use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class Firebase extends Model
{
    use HasFactory;
    use RecordsActivity;
    protected $fillable = ['user_id', 'platform', 'token'];

    public $factory;
    public $messaging;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function __construct($platform = '')
    {
        if($platform == 'dashboard'){
            $file = public_path('/app/bazist-dashboard-firebase.json');
        }
        else{
            $file = public_path('/app/bazist-app-firebase.json');
        }
        $this->factory = (new Factory)->withServiceAccount($file);
        $this->messaging = $this->factory->createMessaging();
    }

    protected $appServerKey = 'AAAAAUQnLnY:APA91bEUHBM9omN2qDrS4OogUSZ3W56ewFWm9gvKN8dLJwwCLacaIXzmYipRvTd8q26oQkKdBlf4JLNNk4l-T_-i95uWMyI8A-EdvncZSw4GQcpMy9uujy4RvaBY6N0yeGhvC_NHJa0D';
    protected $dashboardServerKey = 'AAAATXQMPJ8:APA91bF-mtoYkoetfooWnuuUauXbpqyqZTToLv0jZMub_V60Lg6ODDKWmsIan0xPhp7r8q0dBEjb-wOrTlQ5_T96TZOO1lAp6gktP0JoD_drQldZm_TJbpgJHVo1djlUjyRpjrKLNtx4';

    public $topics = ['all', 'allApp', 'allDriver', 'allDashboard', 'male', 'female', 'mashhad'];

    public $platforms = ['user-app', 'driver-app', 'dashboard'];

    public static function defaultPlatform()
    {
        return 'user-app';
    }

    public function subscribeToTopic($topic,$deviceToken)
    {
        $result = $this->messaging->subscribeToTopic($topic, $deviceToken);
        return $result;
    }

    public function subscribeToTopics($topics,$deviceToken)
    {
        $result = $this->messaging->subscribeToTopics($topics, $deviceToken);
        return $result;
    }

    public function unSubscribeToTopic($topic,$deviceToken)
    {
        $result = $this->messaging->unsubscribeFromTopic($topic, $deviceToken);
        return $result;
    }

    public function unsubscribeFromAllTopics($deviceToken)
    {
        $result = $this->messaging->unsubscribeFromAllTopics($deviceToken);
        return $result;
    }

    public function getInstance($deviceToken)
    {
        $appInstance = $this->messaging->getAppInstance($deviceToken);
        $instanceInfo = $appInstance->rawData();
        return $instanceInfo;
    }

    public function getUserTopics($deviceToken)
    {
        $appInstance = $this->messaging->getAppInstance($deviceToken);
        $subscriptions = $appInstance->topicSubscriptions();

        foreach ($subscriptions as $subscription) {
            echo "{$subscription->registrationToken()} is subscribed to {$subscription->topic()}\n";
        }
    }


    public function send($deviceToken,$data)
    {
        $message = CloudMessage::withTarget('token', $deviceToken)->withData($data);
        $data = $this->messaging->send($message);
        return $data;
    }

    public function sendToTopic($topic,$data)
    {
        $message = CloudMessage::withTarget('topic', $topic)->withData($data);
        $data = $this->messaging->send($message);
        event(new ActivityEvent("ارسال پیام همگانی"));
        return $data;
    }

    public static function dataFormat($data)
    {
        $final = [
            'title'             => isset($data['title']) ? $data['title'] : 'عنوان',
            'message'           => isset($data['message']) ? $data['message'] : 'متن پیام',
            'content'           => isset($data['content']) ? $data['content'] : '',
            'summaryText'       => isset($data['summaryText']) ? $data['summaryText'] : '',
            'showDialog'        => isset($data['showDialog']) ? $data['showDialog'] : 'true',
            'notifId'           => isset($data['notifId']) ? $data['notifId'] : '1',
            'style'             => isset($data['style']) ? $data['style'] : '',
            'priority'          => isset($data['priority']) ? $data['priority'] : 'high',
            'vibrate'           => isset($data['vibrate']) ? $data['vibrate'] : 'true',
        ];
        if(isset($data['actions'])){
            $final['actions'] = $data['actions'];
        }
        return $final;
    }

}
