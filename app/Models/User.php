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
     * Get profile image URL or initials
     */
    public function getProfileImageUrl(): string
    {
        if ($this->profile_image) {
            return Storage::url($this->profile_image);
        }

        // Return initials as a data URI or use a placeholder service
        $initials = $this->getInitials();
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
