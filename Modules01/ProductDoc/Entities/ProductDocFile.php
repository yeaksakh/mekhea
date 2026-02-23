<?php

namespace Modules\ProductDoc\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDocFile extends Model
{
    use SoftDeletes;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'productdoc_files';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'social_id',
        'file_type',
        'file_id',
        'file_unique_id',
        'file_size',
        'file_name',
        'mime_type',
        'duration',
        'width',
        'height',
        'length',
        'thumbnail',
        'thumbnail_path',
        'from_user_id',
        'from_user_name',
        'from_user_username',
        'message_id',
        'message_date',
        'local_path',
        'status'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'thumbnail' => 'array',
        'message_date' => 'datetime',
        'file_size' => 'integer',
        'duration' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'length' => 'integer',
        'from_user_id' => 'integer',
        'message_id' => 'integer',
        'business_id' => 'integer',
        'social_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at'
    ];
    
    /**
     * Get the social configuration that owns the file.
     */
    public function social()
    {
        return $this->belongsTo(ProductDocSocial::class, 'social_id');
    }
    
    /**
     * Get the business that owns the file.
     */
    public function business()
    {
        return $this->belongsTo('App\Models\Business', 'business_id');
    }
    
    /**
     * Scope a query to only include files of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('file_type', $type);
    }
    
    /**
     * Scope a query to only include files with a given status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope a query to only include files from a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromUser($query, $userId)
    {
        return $query->where('from_user_id', $userId);
    }
    
    /**
     * Get the public URL for the file.
     *
     * @return string|null
     */
    public function getPublicUrlAttribute()
    {
        if ($this->local_path) {
            return Storage::url($this->local_path);
        }
        return null;
    }
    
    /**
     * Get the public URL for the thumbnail.
     *
     * @return string|null
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail_path) {
            return asset($this->thumbnail_path);
        }
        return null;
    }
    
    /**
     * Get the human-readable file size.
     *
     * @return string
     */
    public function getHumanFileSizeAttribute()
    {
        if ($this->file_size) {
            $bytes = $this->file_size;
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            
            for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                $bytes /= 1024;
            }
            
            return round($bytes, 2) . ' ' . $units[$i];
        }
        return 'Unknown';
    }
    
    /**
     * Get the formatted duration for videos.
     *
     * @return string
     */
    public function getFormattedDurationAttribute()
    {
        if ($this->duration) {
            $minutes = floor($this->duration / 60);
            $seconds = $this->duration % 60;
            return sprintf('%02d:%02d', $minutes, $seconds);
        }
        return 'Unknown';
    }
    
    /**
     * Check if the file is a video.
     *
     * @return bool
     */
    public function isVideo()
    {
        return $this->file_type === 'video';
    }
    
    /**
     * Check if the file is a video note.
     *
     * @return bool
     */
    public function isVideoNote()
    {
        return $this->file_type === 'video_note';
    }
    
    /**
     * Check if the file is a document.
     *
     * @return bool
     */
    public function isDocument()
    {
        return $this->file_type === 'document';
    }
    
    /**
     * Check if the file is a photo.
     *
     * @return bool
     */
    public function isPhoto()
    {
        return $this->file_type === 'photo';
    }
    
    /**
     * Get the file extension.
     *
     * @return string|null
     */
    public function getFileExtensionAttribute()
    {
        if ($this->file_name) {
            return pathinfo($this->file_name, PATHINFO_EXTENSION);
        }
        return null;
    }
}