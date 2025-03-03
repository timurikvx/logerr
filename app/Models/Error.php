<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Error extends Model
{
    use HasFactory;

    public $timestamps = false;

    public static function writeFromText($text): void
    {
        if(empty($text)){
            return;
        }
        try{
            $data = json_decode($text, true);
        }catch (\Throwable $e){
            return;
        }
        self::write($data);
    }

    public static function write($data): void
    {
        $fields = collect($data);
        $text = $fields->get('text');
        if(is_array($text)){
            $error_text = json_encode($text);
        }else{
            $error_text = $text;
        }
        $date = $fields->get('date');
        if(empty($date)){
            $date = (new \DateTime())->format('Y-m-d H:i:s');
        }

        $error = new Error();
        $error->name = $fields->get('name');
        $error->date = $date;
        $error->guid = $fields->get('guid', '');
        $error->category = $fields->get('category', '');
        $error->sub_category = $fields->get('sub_category', '');
        $error->sender_guid = $fields->get('sender_guid', '');
        $error->sender_name = $fields->get('sender_name', '');
        $error->text = $error_text;
        $error->type = $fields->get('type', '');
        $error->code = $fields->get('code', 0);
        $error->user = $fields->get('user', '');
        $error->device = $fields->get('device', '');
        $error->city = $fields->get('city', '');
        $error->region = $fields->get('region', '');
        $error->version = $fields->get('version', '');
        $error->data = $fields->get('data', '');
        $error->save();
    }

}
