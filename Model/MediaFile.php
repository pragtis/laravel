<?php

namespace App;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model as Eloquent;

class MediaFile extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'media_files';

    /**
     * Method for hasMany relationship with AlbumTrack
     * @param void
     * @return array
     */
    public function album_tracks() {
        return $this->hasMany('App\AlbumTrack');
    }
    
    /**
     * Method for hasMany relationship with AlbumImage
     * @param void
     * @return array
     */
    public function album_images() {
        return $this->hasMany('App\AlbumImage', 'album_id');
    }

    /**
     * Method for hasOne relationship with SocialActivityCount
     * @param void
     * @return array
     */
    public function social_activity_counts() {
        return $this->hasOne('App\SocialActivityCount', 'feed_id');
    }

    /**
     * Method for belongsTo relationship with Playlist
     * @param void
     * @return array
     */
    public function playlists() {
        return $this->belongsTo('App\Playlist', 'playlist_id');
    }
    
    /**
     * Method for belongsTo relationship with VideoGallery
     * @param void
     * @return array
     */
    public function video_gallery() {
        return $this->belongsTo('App\VideoGallery', 'video_gallery_id');
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'title'];
}
