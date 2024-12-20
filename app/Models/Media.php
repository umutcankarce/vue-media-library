<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static $types = [

        'image' => [

            'image/gif',

            'image/avif',

            'image/apng',

            'image/png',

            'image/svg+xml',

            'image/webp',

            'image/jpeg'

        ],

        'audio' => [

            'audio/mpeg',

            'audio/aac',

            'audio/wav',

        ],

        'video' => [

            'video/mp4',

            'video/webm',

            'video/mpeg',

            'video/x-msvideo',

        ],

        'document' => [

            'application/msword',

            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',

            'application/pdf'

        ],

        'archive' => [

            'application/zip',

            'application/x-7z-compressed',

            'application/gzip',

            'application/vnd.rar',

        ],

    ];

    public function author()
    {
        return $this->belongsTo(User::class,'author_id');
    }

    public function getPreviewUrlAttribute()
    {
        $urls = collect([
            'image'    =>  url("storage/media/{$this->created_at->format('Y/m/d')}/{$this->id}/{$this->file_name}"),
            'audio'    =>  asset('images/audio-file.png'),
            'video'    =>  asset('images/video-file.png'),
            'document' =>  asset('images/doc-file.png'),
            'other'    =>  asset('images/other-file.png'),
        ]);

        return $urls[$this->file_type];
    }

    public function getFileTypeAttribute()
    {
        foreach(self::$types as $type => $mimes){
            if(in_array($this->mime_type,$mimes)){
                return $type;
            }
        }

        return 'other';
    }

    public function getUrlAttribute()
    {
        return url("storage/media/{$this->created_at->format('Y/m/d')}/{$this->id}/{$this->file_name}");
    }

    public static function getMimes($fileType)
    {
        return self::$types[$fileType] ?? [];
    }

    public function scopeType(Builder $builder,$type)
    {
        if(!is_null($type)){
            $builder->whereIn('mime_type',self::getMimes($type));
        }
        return $builder;
    }

    public function scopeMonth(Builder $builder,$month)
    {
        if(!is_null($month)){
             $builder->whereBetween('created_at',[
                Carbon::createFromFormat('m-Y',$month)->startOfMonth(),
                Carbon::createFromFormat('m-Y',$month)->endOfMonth()
            ]);
        }
        return $builder;
    }



}
