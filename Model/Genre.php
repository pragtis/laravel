<?php namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Genre extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'genres';
    
    /**
     * To get all genres
     */
    public static function getAllGenres(){
        return \DB::table('genres')->lists('title', 'id');
    }
    
    /**
     * To get genre by limited records and view more option
     */
    public static function getGenreByRestriction($strGenre){
            $strGenreData = '';
        if (!empty($strGenre)) {
            $arrGenre = explode(',', $strGenre);
            $intGenreId = $arrGenre[0];
            $strGenreData = \DB::table('genres')->where('id', $intGenreId)->pluck('title');
            $strGenreData = (count($arrGenre) > 1) ? $strGenreData.'(+ '.(count($arrGenre) - 1).' more)' : $strGenreData;
        }
        return($strGenreData);
    }
}
