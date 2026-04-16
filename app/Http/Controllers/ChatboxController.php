<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatboxController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => ['nullable', 'string', 'max:100'],
            'phone'   => ['nullable', 'string', 'max:50'],
            'content' => ['required', 'string', 'max:2000'],
        ]);

        // Danh sách câu hỏi có sẵn
        $faq = [
            'sản phẩm này có chính hãng không?' => 'Dạ tất cả sản phẩm tại cửa hàng đều là hàng chính hãng 100%, có nguồn gốc rõ ràng.',
            'cửa hàng có miễn phí vận chuyển không?' => 'Dạ cửa hàng có hỗ trợ miễn phí vận chuyển theo chương trình áp dụng.',
            'bao lâu thì nhận được hàng?' => 'Dạ thời gian giao hàng thường từ 1 đến 3 ngày làm việc tùy khu vực.',
            'cửa hàng có đổi trả không?' => 'Dạ cửa hàng có chính sách đổi trả nếu sản phẩm bị lỗi hoặc giao sai.',
            'shop có bán trực tiếp không?' => 'Dạ cửa hàng có hỗ trợ mua trực tiếp và mua online ạ.',
        ];

        $userContent = Str::lower(trim($data['content']));
        $replyContent = null;

        foreach ($faq as $question => $answer) {
            if ($userContent === Str::lower(trim($question))) {
                $replyContent = $answer;
                break;
            }
        }

        // Nếu không khớp FAQ
        if (!$replyContent) {
            $replyContent = 'Câu hỏi của bạn đã được ghi nhận, vui lòng đợi admin trả lời.';
        }

        $sessionId = session()->getId();

        // Lưu tin nhắn khách
        $userMessage = Message::create([
            'name'       => $data['name'] ?? 'Khách hàng',
            'phone'      => $data['phone'] ?? null,
            'session_id' => $sessionId,
            'content'    => $data['content'],
            'from_admin' => 0,
        ]);

        broadcast(new NewChatMessage($userMessage))->toOthers();

        // Lưu tin nhắn trả lời tự động từ admin
        $adminMessage = Message::create([
            'name'       => 'Admin',
            'phone'      => null,
            'session_id' => $sessionId,
            'content'    => $replyContent,
            'from_admin' => 1,
        ]);

        //broadcast(new NewChatMessage($adminMessage))->toOthers();

        return response()->json([
            'ok' => true,
            'user_message_id' => $userMessage->id,
            'reply_message_id' => $adminMessage->id,
            'reply' => $adminMessage->content,
        ]);
    }
}