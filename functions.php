<?php

const API_KEY = '<َAPI:KEY>';

function bot($method,$data){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function sendMessage($chat_id, $text, $reply_to_message_id=null, $reply_markup=null, $parse_mode="markdown", $disable_web_preview=true){
    return bot('sendMessage',['chat_id'=>$chat_id,'reply_to_message_id'=>$reply_to_message_id, 'text'=>$text, 'reply_markup'=>$reply_markup, 'disable_web_page_preview'=>$disable_web_preview, 'parse_mode'=>$parse_mode]);
}

function approveChatJoinRequest($chat_id, $user_id) {
    return bot('approveChatJoinRequest',['chat_id'=>$chat_id,'user_id'=>$user_id]);
}

function checkForTelegramIPs() {
    // Set the ranges of valid Telegram IPs.
    // https://core.telegram.org/bots/webhooks#the-short-version
    $telegram_ip_ranges = [
        ['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], // literally 149.154.160.0/20
        ['lower' => '91.108.4.0', 'upper' => '91.108.7.255'],       // literally 91.108.4.0/22
    ];

    $ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
    $ok     = false;

    foreach ($telegram_ip_ranges as $telegram_ip_range) {
        // Make sure the IP is valid.
        $lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
        $upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
        if ($ip_dec >= $lower_dec && $upper_dec >= $ip_dec) {
            $ok = true;
            break;
        }
    }

    if (!$ok) {
        die("Hmm, I don't trust you...");
    }

}