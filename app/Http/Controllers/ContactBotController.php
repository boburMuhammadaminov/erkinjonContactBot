<?php

namespace App\Http\Controllers;
ini_set('max_execution_time', 0);

use App\Models\ErkinjonUser;
use Illuminate\Http\Request;

class ContactBotController extends Controller
{
    public $admin = "716294792";
    public $token = "5689231162:AAGaQJFKwadscNRz1x6eVwYc8xp3kPkUpUM";
    public function bot($method, $datas = []){
        $url = "https://api.telegram.org/bot".$this->token."/" . $method;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        $res = curl_exec($ch);
        if (curl_error($ch)) {
            var_dump(curl_error($ch));
        } else {
            http_response_code(200);
            return json_decode($res);
        }
    }

    public function index()
    {
        $registerBtn = json_encode([
            'resize_keyboard' => true,
            'keyboard' => [
                [['text' => "Ro'yhatdan o'tish", 'request_contact'=>true]],
            ]
        ]);
        date_default_timezone_set("Asia/Tashkent");
        $update = json_decode(file_get_contents('php://input'));

            if (isset($update->message)) {
//                $this->json($update);

                $message = $update->message;
                $message_id = $message->message_id;
                $chat_id = $message->chat->id;

                $type = $message->chat->type;
                $name = $message->from->first_name;
                $user_id = $message->from->id;
                isset($message->from->username) ? $user = $message->from->username : $user = null;
                isset($message->from->last_name) ? $last_name = $message->from->last_name : $last_name = null;
                isset($message->photo) ? $photo = $message->photo : $photo = null;
                $name = $name . " " . $last_name;


                if ($type == "private") {
                    $checkUser = ErkinjonUser::where('user_id', $user_id)->first();
                    if (!$checkUser) {
                        $mainUser = ErkinjonUser::create([
                            'user_id' => $user_id,
                            'step' => 'new_user',
                        ]);
                    } else {
                        $mainUser = $checkUser;
                    }
                }

                if (isset($message->text)) {

                    $text = $message->text;
                    if ($text == '/start') {
                        $txt = "*Xush kelibsiz, Iltimos ro'yhatdan o'ting!*";
                        $this->sendMessage($chat_id, $txt, ['parse_mode'=>'markdown', 'reply_markup'=>$registerBtn]);
                    }
                }
                if (isset($message->contact)){
                    $this->updateUser($message->contact->user_id, ['phone_number' => $message->contact->phone_number]);
                    $text = "*🆕 New User\n\nName: {$name}\nUser ID: {$user_id}\nPhone: {$message->contact->phone_number}*";
                    $this->sendMessage($this->admin, $text, [
                        'parse_mode' => 'markdown',
                    ]);
                    $txt = "Successfully registered)";
                    $this->sendMessage($chat_id, $txt);
                }

            }
        }


    public function json($update)
    {
        $this->bot('sendMessage', [
            'chat_id' => $this->admin,
            'text' => json_encode($update, JSON_PRETTY_PRINT)
        ]);
    }
    public function updateUser($user_id, $datas){
        return ErkinjonUser::where('user_id', $user_id)->first()->update($datas);
    }
    public function sendMessage($chat_id, $text, $extra = [])
    {
        if (empty($extra)){
            return $this->bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $text
            ]);
        }else{
            return $this->bot('sendMessage', array_merge(
                [
                    'chat_id' => $chat_id,
                    'text' => $text,
                ], $extra
            ));
        }
    }

}
