<?php

namespace App\Http\Controllers\Api;

require __DIR__ . '../../../../../vendor/autoload.php';

use App\Http\Controllers\Controller;
use Telegram\Bot\Api as Telegram;
use Log;
use Telegram\Bot\FileUpload\InputFile; 
use Insta;
use Phpfastcache\Helper\Psr16Adapter;


class Api extends Controller
{
    public function getMessage(){
        $telegram = new Telegram(env('TELEGRAM_BOT_TOKEN'));
        $update = $telegram->getWebhookUpdates();
        return $update->getMessage()->text;
    }
    public function sendMessage($message){
        $telegram = new Telegram(env('TELEGRAM_BOT_TOKEN'));
        $update = $telegram->getWebhookUpdates();
        $telegram->sendMessage([
            'chat_id' => $update->getMessage()->getChat()->getId() , 
            'text' => $message, 
        ]);
    }
    public function sendPhoto($url){
        $telegram = new Telegram(env('TELEGRAM_BOT_TOKEN'));
        $update = $telegram->getWebhookUpdates();
        $telegram->sendPhoto([
            'chat_id' => $update->getMessage()->getChat()->getId() , 
            'photo' => new InputFile($url), 
        ]);
    }

    public function clearUsername(){
        $url = $this->getMessage();
        if (preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i', $url)) {
        $username = substr($url, strpos($url, "m/") + 2);
        return substr($username, 0, strpos($username, "/"));
        }else{
        return $this->getMessage();
        }
    }
    public function getUserInstagram(){
        $instagram  = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), '', '', null);
        $instagram->loginWithSessionId(env("INSTAGRAM_SESSION_ID"));
        return $instagram->getAccount($this->clearUsername());
    }

    public function start(){
        $this->sendMessage("Loading...");
        if($this->getMessage() == '/start'){
            $this->sendMessage("Welcome to Instagram Crawler Bot, please send me your Instagram username or profile link.");
        }else{
        $url =$this->getUserInstagram()['profilePicUrlHd'];
        $fileName = rand().'.png';
        $contents = file_get_contents($url);
        $path = public_path("upload/$fileName");
        file_put_contents($path, $contents);
        $this->sendPhoto($path);
        }
    }
    public function start2(){
        return dd($this->getUserInstagram());
    }
}
