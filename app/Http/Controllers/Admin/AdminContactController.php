<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class AdminContactController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $status = $request->get('status');

        $contacts = Contact::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('subject', 'like', "%{$q}%")
                        ->orWhere('message', 'like', "%{$q}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->appends([
                'q' => $q,
                'status' => $status,
            ]);

        return view('admin.contacts.index', compact('contacts', 'q', 'status'));
    }

    public function show(Contact $contact)
    {
        return view('admin.contacts.show', compact('contact'));
    }

    public function reply(Request $request, Contact $contact)
    {
        $request->validate([
            'reply' => ['required', 'string'],
        ], [
            'reply.required' => 'Vui lòng nhập nội dung phản hồi.',
        ]);

        $contact->update([
            'reply' => $request->reply,
            'status' => 'replied',
        ]);

        return redirect()
            ->route('admin.contacts.show', $contact->id)
            ->with('success', 'Đã phản hồi câu hỏi thành công.');
    }

    public function updateStatus(Request $request, Contact $contact)
    {
        $request->validate([
            'status' => ['required', 'in:pending,replied'],
        ]);

        $contact->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', 'Đã xóa câu hỏi.');
    }
}