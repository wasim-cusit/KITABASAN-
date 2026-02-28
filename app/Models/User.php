<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'password',
        'profile_image',
        'bio',
        'status',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'date_of_birth',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get user initials (first letter of first name and last name)
     */
    public function getInitials(): string
    {
        $firstInitial = $this->first_name
            ? strtoupper(substr($this->first_name, 0, 1))
            : strtoupper(substr($this->name, 0, 1));

        // Try to get last name initial, or second part of name if name is split
        $lastInitial = '';
        if ($this->last_name) {
            $lastInitial = strtoupper(substr($this->last_name, 0, 1));
        } else {
            // Try to split name and get last part initial
            $nameParts = explode(' ', trim($this->name));
            if (count($nameParts) > 1) {
                $lastInitial = strtoupper(substr(end($nameParts), 0, 1));
            }
        }

        return $firstInitial . ($lastInitial ? $lastInitial : '');
    }

    /**
     * Check if user has a profile image that exists on disk.
     */
    public function hasValidProfileImage(): bool
    {
        if (!$this->profile_image) {
            return false;
        }
        return Storage::disk('public')->exists($this->profile_image);
    }

    /**
     * Get the effective profile image URL (for teachers checks TeacherProfile first).
     * Uses storage.serve route (/files/...) so the request always hits Laravel.
     */
    public function getEffectiveProfileImageUrl(): ?string
    {
        $path = null;
        if ($this->teacherProfile && ! empty($this->teacherProfile->profile_image)) {
            $path = $this->teacherProfile->profile_image;
        } elseif (! empty($this->profile_image)) {
            $path = $this->profile_image;
        }
        if (! $path) {
            return null;
        }
        $path = ltrim(str_replace('\\', '/', $path), '/');
        return route('storage.serve', ['path' => $path]);
    }

    /**
     * Whether we have any profile image path to display (user or teacher profile).
     */
    public function hasEffectiveProfileImage(): bool
    {
        if ($this->teacherProfile && ! empty($this->teacherProfile->profile_image)) {
            return true;
        }
        return ! empty($this->profile_image);
    }

    /**
     * Get profile image URL or external placeholder (for backward compatibility).
     * Prefer using getEffectiveProfileImageUrl() + initials in UI.
     */
    public function getProfileImageUrl(): string
    {
        $url = $this->getEffectiveProfileImageUrl();
        if ($url) {
            return $url;
        }
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=random&color=fff&size=200&bold=true&format=png";
    }

    // Relationships
    public function teacherProfile()
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function deviceBindings()
    {
        return $this->hasMany(DeviceBinding::class);
    }

    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function chatbotConversations()
    {
        return $this->hasMany(ChatbotConversation::class);
    }

    public function createdCourses()
    {
        return $this->hasMany(Book::class, 'teacher_id');
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Super admin is the primary admin (admin@kitabasan.com) and cannot be deleted or deactivated.
     */
    public function isSuperAdmin(): bool
    {
        return $this->isAdmin() && $this->email === 'admin@kitabasan.com';
    }

    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
