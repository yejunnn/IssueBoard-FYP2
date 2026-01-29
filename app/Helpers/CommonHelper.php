<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Str;

class CommonHelper
{
    /**
     * Format date for display
     */
    public static function formatDate($date, $format = 'M d, Y H:i')
    {
        if (!$date) return 'N/A';
        
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        return $date->format($format);
    }

    /**
     * Format date as time ago
     */
    public static function timeAgo($date)
    {
        if (!$date) return 'N/A';
        
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        return $date->diffForHumans();
    }

    /**
     * Format file size
     */
    public static function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Generate random string
     */
    public static function randomString($length = 10)
    {
        return Str::random($length);
    }

    /**
     * Generate slug from string
     */
    public static function slugify($string)
    {
        return Str::slug($string);
    }

    /**
     * Truncate text
     */
    public static function truncate($text, $length = 100, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . $suffix;
    }

    /**
     * Get status badge class
     */
    public static function getStatusBadgeClass($status)
    {
        switch (strtolower($status)) {
            case 'open':
                return 'bg-warning';
            case 'in progress':
                return 'bg-info';
            case 'completed':
                return 'bg-success';
            case 'cancel':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Get status icon
     */
    public static function getStatusIcon($status)
    {
        switch (strtolower($status)) {
            case 'open':
                return 'fas fa-clock';
            case 'in progress':
                return 'fas fa-spinner fa-spin';
            case 'completed':
                return 'fas fa-check-circle';
            case 'cancel':
                return 'fas fa-times-circle';
            default:
                return 'fas fa-question-circle';
        }
    }

    /**
     * Get priority badge class
     */
    public static function getPriorityBadgeClass($priority)
    {
        switch (strtolower($priority)) {
            case 'low':
                return 'bg-success';
            case 'medium':
                return 'bg-warning';
            case 'high':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Get department color
     */
    public static function getDepartmentColor($departmentId)
    {
        $colors = [
            '#1e3a8a', '#7c3aed', '#dc2626', '#059669', '#d97706',
            '#7c2d12', '#1e40af', '#be185d', '#15803d', '#a16207',
            '#92400e', '#1e1b4b', '#831843', '#14532d', '#713f12',
            '#0f172a', '#581c87'
        ];
        
        return $colors[$departmentId % count($colors)];
    }

    /**
     * Format phone number
     */
    public static function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 10) {
            return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        }
        
        return $phone;
    }

    /**
     * Mask email address
     */
    public static function maskEmail($email)
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return $email;
        
        $username = $parts[0];
        $domain = $parts[1];
        
        if (strlen($username) <= 2) {
            $maskedUsername = $username;
        } else {
            $maskedUsername = substr($username, 0, 1) . str_repeat('*', strlen($username) - 2) . substr($username, -1);
        }
        
        return $maskedUsername . '@' . $domain;
    }

    /**
     * Generate initials from name
     */
    public static function getInitials($name)
    {
        $words = explode(' ', trim($name));
        $initials = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        
        return substr($initials, 0, 2);
    }

    /**
     * Get avatar color based on name
     */
    public static function getAvatarColor($name)
    {
        $colors = [
            '#f56565', '#ed8936', '#ecc94b', '#48bb78', '#38b2ac',
            '#4299e1', '#667eea', '#9f7aea', '#ed64a6', '#f687b3'
        ];
        
        $hash = crc32($name);
        return $colors[abs($hash) % count($colors)];
    }

    /**
     * Check if user has permission
     */
    public static function hasPermission($user, $permission)
    {
        if (!$user) return false;
        
        if ($user->is_admin) return true;
        
        return false;
        return false;
    }

    /**
     * Get user role display name
     */
    public static function getUserRole($user)
    {
        if ($user->is_admin) {
            return 'Administrator';
        }
        
        if ($user->department) {
            return $user->department->name . ' Staff';
        }
        
        return 'User';
    }

    /**
     * Get ticket statistics
     */
    public static function getTicketStats($departmentId = null)
    {
        $query = \App\Models\Ticket::query();
        
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        
        return [
            'total' => $query->count(),
            'open' => $query->where('status', 'Open')->count(),
            'in_progress' => $query->where('status', 'In Progress')->count(),
            'completed' => $query->where('status', 'Completed')->count(),
            'cancel' => $query->where('status', 'Cancel')->count(),
        ];
    }

    /**
     * Get recent activity
     */
    public static function getRecentActivity($limit = 10)
    {
        return \App\Models\Ticket::with(['category', 'department', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Validate image file
     */
    public static function validateImage($file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024;
        
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.';
        }
        
        if ($file->getSize() > $maxSize) {
            return 'File size too large. Maximum size is 5MB.';
        }
        
        return null;
    }

    /**
     * Upload image
     */
    public static function uploadImage($file, $path = 'uploads')
    {
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->storeAs($path, $filename, 'public');
        
        return $path . '/' . $filename;
    }

    /**
     * Delete image
     */
    public static function deleteImage($path)
    {
        if ($path && file_exists(storage_path('app/public/' . $path))) {
            unlink(storage_path('app/public/' . $path));
        }
    }

    /**
     * Get file extension icon
     */
    public static function getFileIcon($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $icons = [
            'pdf' => 'fas fa-file-pdf',
            'doc' => 'fas fa-file-word',
            'docx' => 'fas fa-file-word',
            'xls' => 'fas fa-file-excel',
            'xlsx' => 'fas fa-file-excel',
            'ppt' => 'fas fa-file-powerpoint',
            'pptx' => 'fas fa-file-powerpoint',
            'txt' => 'fas fa-file-alt',
            'zip' => 'fas fa-file-archive',
            'rar' => 'fas fa-file-archive',
            'jpg' => 'fas fa-file-image',
            'jpeg' => 'fas fa-file-image',
            'png' => 'fas fa-file-image',
            'gif' => 'fas fa-file-image',
            'webp' => 'fas fa-file-image'
        ];
        
        return $icons[$extension] ?? 'fas fa-file';
    }

    /**
     * Generate breadcrumbs
     */
    public static function generateBreadcrumbs($items)
    {
        $breadcrumbs = [];
        
        foreach ($items as $item) {
            if (is_array($item)) {
                $breadcrumbs[] = [
                    'title' => $item['title'],
                    'url' => $item['url'] ?? null,
                    'active' => $item['active'] ?? false
                ];
            } else {
                $breadcrumbs[] = [
                    'title' => $item,
                    'url' => null,
                    'active' => false
                ];
            }
        }
        
        if (!empty($breadcrumbs)) {
            $breadcrumbs[count($breadcrumbs) - 1]['active'] = true;
        }
        
        return $breadcrumbs;
    }

    /**
     * Get pagination info
     */
    public static function getPaginationInfo($paginator)
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }

    /**
     * Format currency
     */
    public static function formatCurrency($amount, $currency = 'USD')
    {
        return number_format($amount, 2) . ' ' . $currency;
    }

    /**
     * Calculate percentage
     */
    public static function calculatePercentage($part, $total, $decimals = 2)
    {
        if ($total == 0) return 0;
        return round(($part / $total) * 100, $decimals);
    }

    /**
     * Get month name
     */
    public static function getMonthName($month)
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        
        return $months[$month] ?? 'Unknown';
    }

    /**
     * Get day name
     */
    public static function getDayName($day)
    {
        $days = [
            0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
            4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'
        ];
        
        return $days[$day] ?? 'Unknown';
    }

    /**
     * Sanitize input to prevent XSS attacks
     */
    public static function sanitizeInput($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        if (is_string($input)) {
            // Remove HTML tags and encode special characters
            $input = strip_tags($input);
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            // Trim whitespace
            $input = trim($input);
        }
        
        return $input;
    }

    /**
     * Validate and sanitize file upload
     */
    public static function validateAndSanitizeFile($file, $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], $maxSize = 2048)
    {
        $errors = [];
        
        if (!$file || !$file->isValid()) {
            $errors[] = 'Invalid file upload.';
            return ['valid' => false, 'errors' => $errors];
        }
        
        // Check MIME type
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'Invalid file type. Allowed types: ' . implode(', ', $allowedMimes);
        }
        
        // Check file size (in KB)
        $fileSizeKB = $file->getSize() / 1024;
        if ($fileSizeKB > $maxSize) {
            $errors[] = "File size exceeds maximum allowed size of {$maxSize}KB.";
        }
        
        // Sanitize filename
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
        $filename = substr($filename, 0, 100); // Limit filename length
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'sanitized_filename' => $filename
        ];
    }
} 