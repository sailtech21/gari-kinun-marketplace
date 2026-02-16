<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        return view('admin.notifications.index');
    }

    public function all()
    {
        $notifications = Notification::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'user_id' => $notification->user_id,
                    'user_name' => $notification->user ? $notification->user->name : 'All Users',
                    'user_email' => $notification->user ? $notification->user->email : null,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'action_url' => $notification->action_url,
                    'is_global' => $notification->is_global,
                    'is_read' => $notification->is_read,
                    'read_at' => $notification->read_at?->format('Y-m-d H:i:s'),
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'icon' => 'nullable|string|max:255',
            'action_url' => 'nullable|url|max:255',
            'is_global' => 'boolean',
        ]);

        // If is_global is true, user_id should be null
        if ($request->boolean('is_global')) {
            $validated['user_id'] = null;
            $validated['is_global'] = true;
        }

        $notification = Notification::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Notification created successfully',
            'notification' => $notification,
        ]);
    }

    public function show($id)
    {
        $notification = Notification::with('user:id,name,email')->findOrFail($id);

        return response()->json([
            'success' => true,
            'notification' => [
                'id' => $notification->id,
                'user_id' => $notification->user_id,
                'user_name' => $notification->user ? $notification->user->name : 'All Users',
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'icon' => $notification->icon,
                'action_url' => $notification->action_url,
                'is_global' => $notification->is_global,
                'is_read' => $notification->is_read,
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'icon' => 'nullable|string|max:255',
            'action_url' => 'nullable|url|max:255',
        ]);

        $notification->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Notification updated successfully',
        ]);
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully',
        ]);
    }

    public function getUsers()
    {
        $users = User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }

    public function stats()
    {
        $total = Notification::count();
        $global = Notification::where('is_global', true)->count();
        $userSpecific = Notification::where('is_global', false)->count();
        $unread = Notification::where('is_read', false)->count();
        $emailSent = Notification::whereNotNull('sent_email_at')->count();
        
        $typeStats = Notification::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();

        return response()->json([
            'total' => $total,
            'global' => $global,
            'user_specific' => $userSpecific,
            'unread' => $unread,
            'email_sent' => $emailSent,
            'by_type' => $typeStats,
        ]);
    }

    public function sendBroadcast(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'icon' => 'nullable|string|max:255',
            'action_url' => 'nullable|url|max:255',
        ]);

        $notification = Notification::create([
            'user_id' => null,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'message' => $validated['message'],
            'icon' => $validated['icon'] ?? 'fa-bell',
            'action_url' => $validated['action_url'] ?? null,
            'is_global' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Broadcast notification sent successfully to all users',
            'notification' => $notification,
        ]);
    }

    public function sendTargeted(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'icon' => 'nullable|string|max:255',
            'action_url' => 'nullable|url|max:255',
        ]);

        $notifications = [];
        foreach ($validated['user_ids'] as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'type' => $validated['type'],
                'title' => $validated['title'],
                'message' => $validated['message'],
                'icon' => $validated['icon'] ?? 'fa-bell',
                'action_url' => $validated['action_url'] ?? null,
                'is_global' => false,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Notification::insert($notifications);

        return response()->json([
            'success' => true,
            'message' => 'Targeted notifications sent to ' . count($validated['user_ids']) . ' users',
            'count' => count($validated['user_ids']),
        ]);
    }

    public function sendEmailToAll(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $users = User::whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        $sentCount = 0;
        foreach ($users as $user) {
            try {
                Mail::raw($validated['message'], function ($mail) use ($user, $validated) {
                    $mail->to($user->email)
                         ->subject($validated['subject']);
                });
                $sentCount++;
            } catch (\Exception $e) {
                \Log::error('Failed to send email to ' . $user->email . ': ' . $e->getMessage());
            }
        }

        // Create notification record
        Notification::create([
            'user_id' => null,
            'type' => 'system',
            'title' => $validated['subject'],
            'message' => $validated['message'],
            'icon' => 'fa-envelope',
            'is_global' => true,
            'sent_email_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Email sent to {$sentCount} users successfully",
            'count' => $sentCount,
        ]);
    }

    public function getNotificationTypes()
    {
        $types = NotificationType::orderBy('priority', 'desc')->get();

        return response()->json([
            'success' => true,
            'types' => $types,
        ]);
    }

    public function toggleNotificationType(Request $request, $id)
    {
        $type = NotificationType::findOrFail($id);
        $type->is_enabled = !$type->is_enabled;
        $type->save();

        return response()->json([
            'success' => true,
            'message' => "Notification type '{$type->label}' " . ($type->is_enabled ? 'enabled' : 'disabled'),
            'is_enabled' => $type->is_enabled,
        ]);
    }

    public function toggleEmailForType(Request $request, $id)
    {
        $type = NotificationType::findOrFail($id);
        $type->email_enabled = !$type->email_enabled;
        $type->save();

        return response()->json([
            'success' => true,
            'message' => "Email notifications for '{$type->label}' " . ($type->email_enabled ? 'enabled' : 'disabled'),
            'email_enabled' => $type->email_enabled,
        ]);
    }

    public function updateNotificationType(Request $request, $id)
    {
        $type = NotificationType::findOrFail($id);
        
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|string|max:255',
            'priority' => 'required|integer',
        ]);

        $type->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Notification type updated successfully',
        ]);
    }
}
