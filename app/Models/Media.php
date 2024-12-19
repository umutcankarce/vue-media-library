<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getPreviewUrlAttribute()
    {
        $urls = collect([
            'image' => [
                'mimes' => ['image/jpeg','image/png','image/jpg'],
                'preview_url' => url("storage/media/{$this->created_at->format('Y/m/d')}/{$this->id}/{$this->file_name}"),
            ],

            'audio' => [
                'mimes' => ['audio/mpeg','audio/wav'],
                'preview_url' => asset('images/audio-file.png'),
            ],
            'video' => [
                'mimes' => [
                    'video/mp4',
                    'video/webm',
                    'video/mpeg',
                    'video/x-msvideo',
                ],
                'preview_url' => asset('images/video-file.png'),
            ],
            'document' => [
                'mimes' => [
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/pdf'
                ],
                'preview_url' => asset('images/doc-file.png'),
            ],
        ]);

        $fileType = $urls ->first(function($item){
            return in_array($this->mime_type,$item['mimes']);
        });

       return $fileType['preview_url'] ?? asset('images/other-file.png');
    }
}
