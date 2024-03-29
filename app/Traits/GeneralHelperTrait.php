<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\SendPushNotification;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use ImageKit\ImageKit;
use Exception;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


trait GeneralHelperTrait
{

    protected function uploadImage($file, $dir='uploads/'){
        $fileName = time() . '-' . $file->getClientOriginalName();
        $file->move('public/'.$dir, $fileName);
        return 'public/'.$dir.$fileName;
    }

    protected function getImage($path){
        if (isset($path) && file_exists($path)){
            return asset($path);
        }else{
            return asset('admin/dist/img/placeholder.png');
        }
    }

    protected function sendMessageToClient($receiverNumber, $message){
        try {
            $account_sid = env('TWILIO_ACCOUNT_SID');
            $auth_token = env('TWILIO_AUTH_TOKEN');
            $twilio_number = env('TWILIO_PHONE_NUMBER');

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber, [
                'from' => $twilio_number,
                'body' => $message]);

            return array('success'=> true, 'message' => 'Message Sent Successfully');

        } catch (Exception $e) {
            return array('success'=> false, 'error' => $e->getMessage());
        }
    }

    protected function sendPushNotification($title, $message, $fcmTokens=array(), $userIds=array()){
        try {
            $userFcmTokens = User::query();
            if (!empty($fcmTokens)){
                $userFcmTokens = $userFcmTokens->whereIn('fcm_token', $fcmTokens);
            }
            if (!empty($userIds)){
                $userFcmTokens = $userFcmTokens->whereIn('id', $fcmTokens);
            }
            $userFcmTokens = $userFcmTokens->whereNotNull('fcm_token')
                ->pluck('fcm_token')->toArray();

            Notification::send(null, new SendPushNotification($title, $message, $userFcmTokens));

            return array('success'=> true, 'message' => 'Notification Sent Successfully');
        }catch (Exception $ex){
            return array('success'=> false, 'error' => $ex->getMessage());
        }
    }

    //Upload Base64 Image
    protected function uploadEncodedImage($image_64, $dir='uploads/'){

        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
      
        $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
      
      // find substring fro replace here eg: data:image/png;base64,
      
       $image = str_replace($replace, '', $image_64); 
      
       $image = str_replace(' ', '+', $image); 
      
       $imageName = Str::random(20).'.'.$extension;

       if(Storage::disk('public_upload')->put($dir.$imageName, base64_decode($image))){
            return url('uploads/'.$dir.$imageName); 
       }else{
            return false;
       }      
    }
}
