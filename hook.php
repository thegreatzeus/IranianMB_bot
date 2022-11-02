<?php
require_once ("functions.php");

checkForTelegramIPs(); // Only proceeds if the request is a legitimate Telegram request.

$confirm_phone_number_keyboard = json_encode(['keyboard'=>[[['text'=>'تایید شماره تلفن 📞','request_contact'=>true]]]]);
$manitoba_area_codes = ["204", "431", "584"];
$main_group_id = "-1001813478950"; // Default group chat ID.

$jsonUpdate = file_get_contents('php://input');
$update = json_decode($jsonUpdate);
@$join_request = $update->chat_join_request;
@$contact = $update->message->contact;


if ($join_request) {
    $chat_id = $join_request->chat->id;
    $user_id = $join_request->from->id;
    $result = sendMessage($user_id, "هموطن گرامی، سلام! 👋\n⚠️ عضویت در گروه ایرانیان مانیتوبا فقط محدود به ساکنین در مانیتوبا می باشد.️\n\nبرای تایید سکونت شما در مانیتوبا، حساب تلگرام شما می بایست با شماره تلفن مانیتوبایی به ثبت رسیده باشد.\nلطفا با کلیک روی دکمه پایین صفحه شماره تلفن خود را با ربات به اشتراک بگزارید 👇", null, $confirm_phone_number_keyboard);
}

if ($contact) {

    $contact_user_id = $contact->user_id;
    $from_id = $update->message->from->id;
    $phone_number = $contact->phone_number;

    if ($contact_user_id !== $from_id) {
        sendMessage($from_id, "شما نمی توانید از شماره تلفن شخص دیگر استفاده کنید.", null, $confirm_phone_number_keyboard);
        return;
    }

    if (strlen($phone_number) != 12) {
        sendMessage($from_id, "شماره تلفن یک شماره کانادایی نیست.", null, $confirm_phone_number_keyboard);
        return;
    }

    if (!str_starts_with($phone_number, '+1')) {
        sendMessage($from_id, "شماره تلفن می بایست با +1 شروع شود.", null, $confirm_phone_number_keyboard);
        return;
    }

    if (!in_array(substr($phone_number, 2, 3), $manitoba_area_codes)) {
        sendMessage($from_id, "کد منطقه می بایست یکی از کد های مانیتوبا باشد (204, 431, 584).", null, $confirm_phone_number_keyboard);
        return;
    }

    approveChatJoinRequest($main_group_id, $from_id);
    $result = sendMessage($from_id, "شماره تلفن شما تایید شد 🙌\n\nبه گروه ایرانیان مانیتوبا خوش آمدید! 😊");
}

