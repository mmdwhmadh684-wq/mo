<?php
ob_start();
$token = "8507254017:AAFAAAIEZ5KXm9jiCAiCEdgpm87-0kpCSyg";
define("API_KEY", $8507254017:AAFAAAIEZ5KXm9jiCAiCEdgpm87-0kpCSyg);
$admin = "8960841099";
$dev_admin = 8960841099;
$log_channel = "@ALZAEMMM890";
//ملاحظة خلي القناة خاصة حتى تشاهد الملفات التي تمر من البوت هل هي ملغمة أو لا وتفحص  الملف قبل رفعة
$update = json_decode(file_get_contents("php://input"));
$message = $update->message ?? null;
$callback_query = $update->callback_query ?? null;
$text = $message->text ?? "";
$from_id = $message->from->id ?? ($callback_query->from->id ?? 0);
$chat_id = $message->chat->id ?? ($callback_query->message->chat->id ?? 0);
$message_id = $message->message_id ?? ($callback_query->message->message_id ?? 0);
$data = $callback_query->data ?? "";
$name = $message->from->first_name ?? ($callback_query->from->first_name ?? "مجهول");
$username = $message->from->username ?? ($callback_query->from->username ?? "لا يوجد");
$type = $message->chat->type ?? ($callback_query->message->chat->type ?? "private");

$botFile = "bot.json";
$bot = file_exists($botFile) ? json_decode(file_get_contents($botFile), true) : [];
$eshterakFile = "eshterak.json";
$eshterak = file_exists($eshterakFile) ? json_decode(file_get_contents($eshterakFile), true) : [];
$abdoFile = "abdo.json";
$abdo = file_exists($abdoFile) ? json_decode(file_get_contents($abdoFile), true) : [];

function saveData() {
    global $bot, $eshterak, $abdo;
    file_put_contents("bot.json", json_encode($bot, JSON_PRETTY_PRINT));
    file_put_contents("eshterak.json", json_encode($eshterak, JSON_PRETTY_PRINT));
    file_put_contents("abdo.json", json_encode($abdo, JSON_PRETTY_PRINT));
}

function s() {
    saveData();
}

function worker($cmd) {
    if (stristr(PHP_OS, 'WIN')) {
        pclose(popen("start /B $cmd", "r"));
    } else {
        exec("$cmd > /dev/null 2>&1 &");
    }
}

function bot($method, $datas = []) {
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        return (object)['ok' => false, 'error' => curl_error($ch)];
    }
    curl_close($ch);
    return json_decode($res);
}

function send_message($message, $chat_id, $tk) {
    $url = "https://api.telegram.org/bot" . $tk . "/sendMessage?chat_id=" . $chat_id;
    $url .= "&text=" . urlencode($message);
    $url .= "&parse_mode=markdown";
    @file_get_contents($url);
}

function chatWithAI($msg) {
    $url = 'https://api.flexgpt.live/api/chat/526';
    $headers = [
        'authority: api.flexgpt.live',
        'accept: application/json, text/plain, */*',
        'accept-language: ar-EG,ar;q=0.9,en-US;q=0.8,en;q=0.7',
        'authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1laWQiOiIzMTk1MDUiLCJ1bmlxdWVfbmFtZSI6IlZBUyBVc2VyIiwibmJmIjoxNzg0MDc3MjkwLCJleHAiOjE3ODQ2ODIwOTAsImlhdCI6MTc4NDA3NzI5MH0.bGZnVKFIaLJh8WsxqmIX3yzOxzpYK4f0RQ8d5eumOic',
        'content-type: application/json',
        'origin: https://demo.flexgpt.live',
        'referer: https://demo.flexgpt.live/',
        'sec-ch-ua: "Chromium";v="139", "Not;A=Brand";v="99"',
        'sec-ch-ua-mobile: ?1',
        'sec-ch-ua-platform: "Android"',
        'sec-fetch-dest: empty',
        'sec-fetch-mode: cors',
        'sec-fetch-site: same-site',
        'user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36'
    ];
    $json_data = json_encode(['message' => $msg, 'isWebSearch' => false]);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    return $data['response'] ?? 'عذراً، حدث خطأ في الاتصال بالذكاء الاصطناعي.';
}

function logToChannel() {
    global $update, $message, $callback_query, $text, $from_id, $chat_id, $name, $username, $log_channel, $type, $admin, $dev_admin;
    
    if (!$message || $type !== "private" || $from_id == $admin || $from_id == $dev_admin) return;
    
    $user_link = "[$name](tg://user?id=$from_id)";
    $user_username = $username !== "لا يوجد" ? "@$username" : "لا يوجد";
    
    $base_info = "🔔 **إشعار جديد**\n\n";
    $base_info .= "⌯︰الاسم: $user_link\n";
    $base_info .= "⌯︰اليوزر: $user_username\n";
    $base_info .= "⌯︰الأيدي: `$from_id`\n";
    
    if (!empty($text)) {
        $content_type = "💬 رسالة نصية";
        $preview = mb_substr($text, 0, 200);
        if (mb_strlen($text) > 200) $preview .= "...";
        
        bot("sendMessage", [
            "chat_id" => $log_channel,
            "text" => $base_info . "⌯︰النوع: $content_type\n⌯︰المحتوى:\n`$preview`",
            "parse_mode" => "markdown"
        ]);
        return;
    }
    
    if (isset($message->photo)) {
        $photos = $message->photo;
        $photo = end($photos);
        $caption = $message->caption ?? "بدون وصف";
        
        bot("sendPhoto", [
            "chat_id" => $log_channel,
            "photo" => $photo->file_id,
            "caption" => $base_info . "⌯︰النوع: 🖼 صورة\n⌯︰الوصف: $caption",
            "parse_mode" => "markdown"
        ]);
        return;
    }
    
    if (isset($message->video)) {
        $caption = $message->caption ?? "بدون وصف";
        bot("sendVideo", [
            "chat_id" => $log_channel,
            "video" => $message->video->file_id,
            "caption" => $base_info . "⌯︰النوع: 🎥 فيديو\n⌯︰الوصف: $caption",
            "parse_mode" => "markdown"
        ]);
        return;
    }
    
    if (isset($message->document)) {
        $file_name = $message->document->file_name ?? "بدون اسم";
        $caption = $message->caption ?? "";
        bot("sendDocument", [
            "chat_id" => $log_channel,
            "document" => $message->document->file_id,
            "caption" => $base_info . "⌯︰النوع: 📄 ملف\n⌯︰اسم الملف: `$file_name`\n⌯︰الوصف: $caption",
            "parse_mode" => "markdown"
        ]);
        return;
    }
    
    if (isset($message->voice)) {
        bot("sendVoice", [
            "chat_id" => $log_channel,
            "voice" => $message->voice->file_id,
            "caption" => $base_info . "⌯︰النوع: 🎤 رسالة صوتية",
            "parse_mode" => "markdown"
        ]);
        return;
    }
    
    if (isset($message->audio)) {
        $file_name = $message->audio->file_name ?? "بدون اسم";
        bot("sendAudio", [
            "chat_id" => $log_channel,
            "audio" => $message->audio->file_id,
            "caption" => $base_info . "⌯︰النوع: 🎵 ملف صوتي\n⌯︰الاسم: `$file_name`",
            "parse_mode" => "markdown"
        ]);
        return;
    }
    
    if (isset($message->sticker)) {
        bot("sendSticker", [
            "chat_id" => $log_channel,
            "sticker" => $message->sticker->file_id
        ]);
        bot("sendMessage", [
            "chat_id" => $log_channel,
            "text" => $base_info . "⌯︰النوع: 🎭 ملصق",
            "parse_mode" => "markdown"
        ]);
        return;
    }
    
    if (isset($message->contact)) {
        $contact_name = $message->contact->first_name ?? "";
        $phone = $message->contact->phone_number ?? "";
        bot("sendMessage", [
            "chat_id" => $log_channel,
            "text" => $base_info . "⌯︰النوع: 📞 جهة اتصال\n⌯︰الاسم: $contact_name\n⌯︰الرقم: `$phone`",
            "parse_mode" => "markdown"
        ]);
        return;
    }
    
    if (isset($message->location)) 
        $lat = $message->location->latitude;
        $lon = $message->location->longitude;
        bot("sendMessage", [
            "chat_id" => $log_channel,
            "text" => $base_info . "⌯︰النوع: 📍 موقع\n⌯︰الإحداثيات: $lat, $lon",
            "parse_mode" => "markdown"
        ]);
        return;
    }


if ($message) {
    logToChannel();
}

function checkSubscription($user_id) {
    global $eshterak, $admin, $dev_admin;
    
    if (empty($eshterak)) return true;
    if ($user_id == $admin || $user_id == $dev_admin) return true;
    
    foreach ($eshterak as $channel_id => $val) {
        $check = bot("getChatMember", [
            "chat_id" => $channel_id,
            "user_id" => $user_id
        ]);
        
        if (!$check || !$check->ok) continue;
        
        $status = $check->result->status ?? "";
        if (!in_array($status, ['member', 'administrator', 'creator'])) {
            return false;
        }
    }
    
    return true;
}

function sendSubscribeMessage($chat_id) {
    global $eshterak;
    
    $channels_text = "📢 **للاستمرار في استخدام البوت، يجب عليك الاشتراك في القنوات التالية:**\n\n";
    
    $keyboard = [];
    foreach ($eshterak as $channel_id => $val) {
        $channel_info = bot("getChat", ["chat_id" => $channel_id]);
        if ($channel_info && $channel_info->ok) {
            $channel_username = $channel_info->result->username ?? "";
            $channel_title = $channel_info->result->title ?? "قناة";
            
            if ($channel_username) {
                $channels_text .= "• [$channel_title](https://t.me/$channel_username)\n";
                $keyboard[] = [['text' => "📢 $channel_title", 'url' => "https://t.me/$channel_username", 'style' => 'primary']];
            }
        }
    }
    
    $channels_text .= "\n**بعد الاشتراك، اضغط على زر ✅ تم الاشتراك**";
    $keyboard[] = [['text' => "✅ تم الاشتراك", 'callback_data' => "check_subscribe", 'style' => 'primary']];
    
    bot("sendMessage", [
        "chat_id" => $chat_id,
        "text" => $channels_text,
        "parse_mode" => "markdown",
        "reply_markup" => json_encode(['inline_keyboard' => $keyboard])
    ]);
}

if ($message && $type == "private" && $from_id != $admin && $from_id != $dev_admin) {
    if (!empty($eshterak) && !checkSubscription($from_id)) {
        sendSubscribeMessage($chat_id);
        exit;
    }
}

if ($callback_query && $from_id != $admin && $from_id != $dev_admin && !empty($eshterak)) {
    $skip_check = ["check_subscribe", "bot"];
    if (!in_array($data, $skip_check) && !checkSubscription($from_id)) {
        bot('answerCallbackQuery', [
            'callback_query_id' => $callback_query->id,
            'text' => "⚠️ يجب الاشتراك في القنوات أولاً!",
            'show_alert' => true
        ]);
        sendSubscribeMessage($chat_id);
        exit;
    }
}

if ($data == "check_subscribe") {
    if (checkSubscription($from_id)) {
        bot('answerCallbackQuery', [
            'callback_query_id' => $callback_query->id,
            'text' => "✅ تم الاشتراك بنجاح!",
            'show_alert' => true
        ]);
        
        bot("deleteMessage", [
            "chat_id" => $chat_id,
            "message_id" => $message_id
        ]);
        
        $photo_url = "https://i.ibb.co/bgfssH8m/x.jpg";
        $user_link = "[$name](tg://user?id=$from_id)";
        
        $welcome_text = "⌯︰أهلاً بك سيدي: $user_link\n";
        $welcome_text .= "⌯︰وظيفتي استقبال ملفاتك بأمان\n";
        $welcome_text .= "⌯︰ارفع ملفك الآن بسهولة ❗\n\n";
        $welcome_text .= "︰Writing in English for programming";
        
        $keyboard = json_encode([
            'inline_keyboard' => [
                [['text' => "- رفع ملف PHP", 'callback_data' => "upload_php", 'style' => 'primary']],
                [['text' => "- التعليمات 🗞️", 'callback_data' => "rules", 'style' => 'primary'], ['text' => "تحدث مع AI 👾", 'callback_data' => "chat_ai", 'style' => 'primary']],
                [['text' => "- Developer 🪪", 'url' => "https://t.me/b9kkn", 'style' => 'primary'], ['text' => "- Channel 🛍️", 'url' => "https://t.me/SourceHassani", 'style' => 'primary']]
            ]
        ]);
        
        bot("sendPhoto", [
            "chat_id" => $chat_id,
            "photo" => $photo_url,
            "caption" => $welcome_text,
            "parse_mode" => "markdown",
            'reply_markup' => $keyboard
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $callback_query->id,
            'text' => "⚠️ لم تشترك بعد!",
            'show_alert' => true
        ]);
    }
    exit;
}

$statsFile = 'statistics.json';
$stats = file_exists($statsFile) ? json_decode(file_get_contents($statsFile), true) : ["users" => [], "groups" => [], "stats" => ["total_users" => 0]];

if ($type == "private" && !in_array($from_id, $stats['users'])) {
    $stats['users'][] = $from_id;
    $stats['stats']['total_users']++;
    file_put_contents($statsFile, json_encode($stats, JSON_PRETTY_PRINT));
} elseif ($type == "supergroup" && !in_array($chat_id, $stats['groups'])) {
    $stats['groups'][] = $chat_id;
    file_put_contents($statsFile, json_encode($stats, JSON_PRETTY_PRINT));
}

if (($text == '/start' or $data == 'bot') and $from_id == $admin) {
    $m = $data ? 'EditMessageText' : 'sendMessage';
    bot($m, [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "أهلاً بك عزيزي المطور في لوحة التحكم الخاصة بك ⚙️\n\n[قناة السورس](https://t.me/INNV8)\n⚙️ — — — — — — — — ⚙️",
        'parse_mode' => "markdown",
        'disable_web_page_preview' => true,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "📢 الاشتراك الإجباري", 'callback_data' => "eshterak", 'style' => 'primary']],
                [['text' => "📣 الاذاعة", 'callback_data' => "msg", 'style' => 'primary']],
                [['text' => "👥 عدد المستخدمين الكلي", 'callback_data' => "total_users", 'style' => 'primary'], ['text' => "📁 عدد الملفات", 'callback_data' => "total_files", 'style' => 'primary']]
            ]
        ])
    ]);
    $abdo['mode'][$from_id]['mode'] = null;
    s();
    if ($data) exit;
}

if ($data == "total_users") {
    $totalUsers = $stats['stats']['total_users'] ?? count($stats['users']);
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "📊 **عدد المستخدمين الكلي المشتركين في البوت:** `$totalUsers` مستخدم.",
        'parse_mode' => "markdown",
        'reply_markup' => json_encode([
            'inline_keyboard' => [[['text' => "• رجوع •", 'callback_data' => "bot", 'style' => 'primary']]]
        ])
    ]);
    exit;
}

if ($data == "total_files") {
    $totalFiles = count(glob("*.*"));
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "📁 **عدد ملفات البوت الإجمالي في السيرفر:** `$totalFiles` ملف.",
        'parse_mode' => "markdown",
        'reply_markup' => json_encode([
            'inline_keyboard' => [[['text' => "• رجوع •", 'callback_data' => "bot", 'style' => 'primary']]]
        ])
    ]);
    exit;
}

if ($data == "eshterak") {
    bot("EditMessageText", [
        "chat_id" => $chat_id,
        "message_id" => $message_id,
        "text" => "مرحبا بك في قسم الاشتراك الإجباري. اختر الإجراء المطلوب:",
        "parse_mode" => "markdown",
        "reply_markup" => json_encode([
            "inline_keyboard" => [
                [["text" => "➕ أضف قناة", "callback_data" => "esh", 'style' => 'primary'], ["text" => "❌ حذف قناة", "callback_data" => "unesh", 'style' => 'primary']],
                [["text" => "👁 عرض قنوات الاشتراك الإجباري", "callback_data" => "eshh", 'style' => 'primary']],
                [["text" => "🗑 حذف جميع القنوات", "callback_data" => "uneshh", 'style' => 'primary']],
                [['text' => "• رجوع •", 'callback_data' => "bot", 'style' => 'primary']]
            ]
        ])
    ]);
    $abdo['mode'][$from_id]['mode'] = null;
    s();
    exit;
}

if ($data == "esh") {
    bot("EditMessageText", [
        "chat_id" => $chat_id,
        "message_id" => $message_id,
        "text" => "👤 أرسل معرف القناة (@channel)، أيدي القناة، أو قم بتوجيه رسالة من القناة المراد إضافتها.",
        "reply_markup" => json_encode([
            "inline_keyboard" => [[["text" => "• إلغاء •", "callback_data" => "eshterak", 'style' => 'primary']]]
        ])
    ]);
    $abdo['mode'][$from_id]['mode'] = "esh_step1";
    s();
    exit;
}

if ($message && isset($abdo['mode'][$from_id]['mode']) && $abdo['mode'][$from_id]['mode'] == "esh_step1") {
    $channel_id = null;
    if (strpos($text, "@") === 0) {
        $channel_info = bot("getChat", ["chat_id" => $text]);
        if ($channel_info && $channel_info->ok) {
            $channel_id = $channel_info->result->id;
        }
    } elseif (is_numeric($text)) {
        $channel_id = (strpos($text, "-100") !== 0) ? "-100" . $text : $text;
    } elseif (isset($message->forward_from_chat) && $message->forward_from_chat) {
        $channel_id = $message->forward_from_chat->id;
    }

    if ($channel_id) {
        $eshterak[$channel_id] = true;
        $abdo['mode'][$from_id]['mode'] = null;
        s();
        $get_title = bot("getChat", ["chat_id" => $channel_id]);
        $channel_name = $get_title->result->title ?? "قناة غير معروفة";
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "✅ تمت إضافة القناة ($channel_name) لقائمة الاشتراك الإجباري بنجاح.",
        ]);
    } else {
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "⚠️ لم أتمكن من استخراج آيدي القناة. يرجى المحاولة مرة أخرى أو التأكد من رفع البوت أدمن فيها.",
        ]);
    }
    exit;
}

if ($data == "unesh") {
    bot("EditMessageText", [
        "chat_id" => $chat_id,
        "message_id" => $message_id,
        "text" => "🗑️ أرسل معرف أو أيدي القناة التي تريد حذفها من قائمة الاشتراك الإجباري.",
        "reply_markup" => json_encode([
            "inline_keyboard" => [[["text" => "• رجوع •", "callback_data" => "eshterak", 'style' => 'primary']]]
        ])
    ]);
    $abdo['mode'][$from_id]['mode'] = "unesh";
    s();
    exit;
}

if ($message && isset($abdo['mode'][$from_id]['mode']) && $abdo['mode'][$from_id]['mode'] == "unesh") {
    $channel_id = null;
    if (strpos($text, "@") === 0) {
        $channel_info = bot("getChat", ["chat_id" => $text]);
        if ($channel_info && $channel_info->ok) {
            $channel_id = $channel_info->result->id;
        }
    } elseif (is_numeric($text)) {
        $channel_id = (strpos($text, "-100") !== 0) ? "-100" . $text : $text;
    } elseif (isset($message->forward_from_chat) && $message->forward_from_chat) {
        $channel_id = $message->forward_from_chat->id;
    }

    if ($channel_id && isset($eshterak[$channel_id])) {
        unset($eshterak[$channel_id]);
        s();
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "✅ تم حذف القناة من قائمة الاشتراك الإجباري.",
        ]);
        $abdo['mode'][$from_id]['mode'] = null;
    } else {
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "❌ القناة غير موجودة بالفعل في قائمة الاشتراك الإجباري.",
        ]);
    }
  //  exit;
}
        
if ($data == "eshh") {
    if (!empty($eshterak)) {
        $eshterak_list = "📋 **قنوات الاشتراك الإجباري المضافة حالياً:**\n";
        foreach ($eshterak as $channel_id => $val) {
            $get = bot("getChat", [
                "chat_id" => $channel_id
            ]);
            $title = $get->result->title ?? $channel_id;
            $eshterak_list .= "- $title (`$channel_id`)\n";
        }
    }
}
        
            


       
