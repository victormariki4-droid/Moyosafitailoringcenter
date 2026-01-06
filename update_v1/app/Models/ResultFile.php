<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ResultFile extends Model
{
    protected $fillable = [
        'result_id',
        'file_path',
        'original_name',
        'uploaded_by',
    ];

    protected $appends = [
        'url',
        'is_image',
        'is_pdf',
    ];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ✅ File public URL (storage/public)
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    // ✅ Helpers for UI
    public function getIsImageAttribute(): bool
    {
        $ext = strtolower(pathinfo((string) $this->file_path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    }

    public function getIsPdfAttribute(): bool
    {
        $ext = strtolower(pathinfo((string) $this->file_path, PATHINFO_EXTENSION));
        return $ext === 'pdf';
    }
}
