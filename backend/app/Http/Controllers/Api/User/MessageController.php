<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $paginate = 10;
        $data = ['limit' => $paginate, 'list' => []];
        $messages = Message::orderBy('id', 'DESC')->paginate($paginate);
        foreach ($messages as $message){
            $data['list'][] = [
                'title'   => $message->title,
                'date'    => verta()->instance($message->created_at)->format('Y/m/d H:i'),
                'message' => $message->text,
                'path'    =>  '',
            ];
        }
        return sendJson('success','',$data);
    }
}
