<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AnnouncementMail;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'category',
        'user_id',
        'region_id',
        'external_link',
        'pdf_path',
        'is_active',
    ];

    /**
     * Relationship: The user who created the announcement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: The region this announcement is scoped to (Null = Global).
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Model Booted Method for Auto Email Notifications
     */
    protected static function booted()
    {
        static::created(function ($announcement) {
            try {
                // Build recipient query
                $recipientQuery = User::whereNotNull('email')
                    ->whereIn('role', ['President', 'Coordinator', 'Member']);

                // If scoped to a specific region, notify ONLY users in that region
                if ($announcement->region_id) {
                    $recipientQuery->where('region_id', $announcement->region_id);
                }

                $recipients = $recipientQuery->pluck('email');

                // Queue emails for each recipient
                foreach ($recipients as $email) {
                    Mail::to($email)->queue(new AnnouncementMail($announcement));
                }

            } catch (\Exception $e) {
                // Log failure so application flow doesn't break
                Log::error("Email notification failed for Announcement {$announcement->id}: " . $e->getMessage());
            }
        });
    }
}