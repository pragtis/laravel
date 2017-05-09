<?php namespace App\Http\Controllers;

use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Cookie\CookieJar;
use Illuminate\Pagination\LengthAwarePaginator;
use View;
use App\User;
use App\MediaFile;
use App\AlbumTrack;
use App\TaggedUser;
use App\TaggedMedia;
use App\SongTaggedUser;
use App\Notification;
use App\UserRemainingSong;
use App\PlaylistDetails;
use Mail;
use Auth;
use Config;
use Illuminate\Http\Response;
use Route;
use App\Http\Middleware\ResizeImageComp;
use App\Libraries\aws\AwsS3;

class MediaController extends Controller {
    /**
     * Method to get vimeo video info
     * @param type $videoId
     */
    private function getVimeoVideoInfo($videoId) {
        $url = str_replace('{videoId}', $videoId, Config::get('constants.VIMEO_VIDEO_INFO'));
        $ch = curl_init($url);
        //curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, '3');
        $content = trim(curl_exec($ch));
        curl_close($ch);
        return($content);
    }

    /**
     * Method to get youtube video info
     * @param type $videoId
     */
    private function getYoutubeVideoInfo($videoId) {
        $developerKey = Config::get('constants.GOOGLE_DEVELOPER_KEY');
        $url = str_replace(array('{id}', '{key}'), array($videoId, $developerKey), Config::get('constants.GOOGLE_VIDEO_INFO'));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return($output);
    }

    /**
     * view media in admin
     */
    public function viewMedia($mediaId = null) {
        if (!Session::has('ADMIN_USER')) {
            return Redirect::to('/admin/user/login');
        }
        if (empty($mediaId)) {
            return Redirect::to('/admin/manage/mediaFiles');
        }

        $arrMedia = MediaFile::with(array('album_tracks', 'social_activity_counts'))
                        ->where('media_files.status', 1)
                        ->where('media_files.isDeleted', 0)
                        ->where('media_files.id', $mediaId)
                        ->select('media_files.id', 'media_files.media_type', 'media_files.media_path', 'media_files.title', 'media_files.description', 'media_files.user_id', 'media_files.created_at', 'media_files.isApproved', 'media_files.location', 'media_files.start_date', 'media_files.image', 'media_files.total_hits', 'media_files.video_reference', 'media_files.media_image_large')
                        ->first()->toArray();
        $metaTags = DB::table('meta_tags')->where('media_id', $mediaId)->first();
        $playlistDetails = DB::table('playlist_details')->select('album_track_id')->get();
        $lookup_array = array();
        foreach ($playlistDetails as $arr) {
            $lookup_array[$arr->album_track_id] = 1;
        }
        return view('media/viewMedia')->with(compact('arrMedia', 'metaTags', 'lookup_array'));
    }

    /**
     * To embed media
     *
     * @return Response
     */
    public function embed($mediaId) {
        $mediaId = base64_decode($mediaId);
        $strRouteName = Route::currentRouteName();

        //Get user profile info
        $userProfile = DB::table('media_files')
                ->leftJoin('users', 'media_files.user_id', '=', 'users.id')
                ->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
                ->select('profiles.profile_pic', 'users.username', 'users.id', 'profiles.name', 'media_files.free_download', 'media_files.buy_beatsta', 'media_files.price', 'media_files.purchase_link', 'media_files.media_type', 'media_files.media_image', 'media_files.id as media_id')
                ->where('media_files.id', $mediaId)
                ->first();

        $objMedia = new MediaFile();
        $mediaDownCount = $objMedia->getMediaDownloadCount($mediaId);
        $albumPlayedCount = $objMedia->getAlbumPlayedCount($mediaId);

        $arrMedia = MediaFile::with(array('album_tracks', 'social_activity_counts'))
                ->where('media_files.status', 1)
                ->where('media_files.isDeleted', 0)
                ->where('id', $mediaId)
                ->select('media_files.id', 'media_files.media_type', 'media_files.media_path', 'media_files.title', 'media_files.description', 'media_files.user_id', 'media_files.total_hits', 'media_files.avg_rating', 'media_files.created_at', 'media_files.location', 'media_files.image', 'media_files.video_reference', 'media_files.media_image', 'media_files.buy_beatsta', 'media_files.free_download', 'media_files.price', 'media_files.purchase_link')
                ->first()
                ->toArray();
        return View::make('media.embed')->with(compact('mediaId', 'arrMedia', 'userProfile', 'mediaDownCount', 'albumPlayedCount'));
    }

    /**
     * Method to get music, artists, videos according to country or genre
     * @param type $country
     * @return view
     */
    public function getCountryMedia($countryOrGenre = null) {
        $strRouteName = Route::currentRouteName();
        
        $intGenreCountryCodeId = '';
        
        $arrFeaturedArtists = [];
        $arrLatestArtists = [];
        $arrTrendingArtists = [];
        $arrGenre = [];
        $arrCountry = [];
        
        /* genre or country info */
        if (in_array($strRouteName, ['countryArtists', 'countryAlbums', 'countryVideos'])) {
            $intGenreCountryCodeId = $countryOrGenre;
            $arrCountry = DB::table('countries')->where('new_country_code', $countryOrGenre)->first();
        }
        if (in_array($strRouteName, ['genreArtists', 'genreAlbums', 'genreVideos'])) {
            $arrGenre = DB::table('genres')->where('alias', $countryOrGenre)->first();
            $intGenreCountryCodeId = $arrGenre->id;
        }
        
        /* artists, album, videos info */
        if (in_array($strRouteName, ['countryArtists', 'genreArtists'])) {
            $arrFeaturedArtists = $this->getArtists('featured', $strRouteName, $strSrchKeyword='', $intGenreCountryCodeId);
            $arrLatestArtists = $this->getArtists('latest', $strRouteName, $strSrchKeyword='', $intGenreCountryCodeId);
            $arrTrendingArtists = $this->getArtists('trending', $strRouteName, $strSrchKeyword='', $intGenreCountryCodeId);
        }
        if (in_array($strRouteName, ['countryAlbums', 'genreAlbums'])) {
            $arrFeaturedAlbums = $this->getMedia('featured', 'a', $strRouteName, $strSrchKeyword='', $intGenreCountryCodeId);
            $arrLatestAlbums = $this->getMedia('latest', 'a', $strRouteName, $strSrchKeyword='', $intGenreCountryCodeId);
            $arrTrendingAlbums = $this->getMedia('trending', 'a', $strRouteName, $strSrchKeyword='', $intGenreCountryCodeId);
        }
        if (in_array($strRouteName, ['countryVideos', 'genreVideos'])) {
            $arrFeaturedVideos = $this->getMedia('featured', 'v', $strRouteName, $strSrchKeyword='', $intGenreCountryCodeId);
            $arrLatestVideos = $this->getMedia('latest', 'v', $strRouteName, $strSrchKeyword='', $intGenreCountryCodeId);
            $arrTrendingVideos = $this->getMedia('trending', 'v', $strRouteName, $strSrchKeyword='', $intGenreCountryCodeId);
        }
        
        /* redering view */
        return view('media.view_more_media')->with(compact('arrFeaturedArtists', 'arrLatestArtists', 'arrTrendingArtists', 'arrFeaturedAlbums', 'arrLatestAlbums', 'arrTrendingAlbums', 'arrFeaturedVideos', 'arrLatestVideos', 'arrTrendingVideos', 'arrGenre', 'arrCountry', 'strRouteName', 'countryOrGenre'));
    }
    
    /**
     * To return media info
     *
     * @return Response
     */
    private function getMedia($type, $mediaType, $strRouteName, $strSrchKeyword, $intGenreCountryCodeId) {
        $query = DB::table('media_files');
        $query->leftJoin('users', 'media_files.user_id', '=', 'users.id');
        $query->leftJoin('profiles', 'users.id', '=', 'profiles.user_id');
        $query->join('countries', 'countries.id', '=', 'profiles.country_id');
        $query->select('profiles.id as profileID', 'users.id as userID', 'users.username', 'media_files.media_type', 'media_files.id as mediaID', 'media_files.media_path', 'media_files.media_image','media_files.media_image_large','media_files.video_reference','media_files.title','media_files.media_image', 'profiles.name','media_files.genre');
        
        if (in_array($strRouteName, ['genreAlbums', 'genreVideos']) && !empty($intGenreCountryCodeId)) {
            $query->whereRaw("FIND_IN_SET($intGenreCountryCodeId, media_files.genre)");
        }
        if (in_array($strRouteName, ['countryAlbums', 'countryVideos']) && !empty($intGenreCountryCodeId)) {
            $query->where('countries.new_country_code', $intGenreCountryCodeId);
        }
        if (!empty($strSrchKeyword)) {
            $query->whereRaw("(media_files.title LIKE '%" . $strSrchKeyword . "%' OR media_files.description LIKE '%" . $strSrchKeyword . "%')");
        }
        $query->where('media_files.media_type', $mediaType);
        $query->where('media_files.isDeleted','0');
        $query->where('media_files.status','1');
        
        if ($type == 'featured') {
            return $query->where('media_files.is_featured', 1)->orderBy('media_files.updated_at', 'DESC')->take(100)->get();
        }
        if ($type == 'latest') {
            return  $query->orderBy('media_files.id', 'DESC')->take(100)->get();
        }
        if ($type == 'trending') {
            return $query->orderBy('media_files.total_hits', 'DESC')->take(100)->get();
        }
    }
    
    /**
     * To return artist info
     *
     * @return Response
     */
    private function getArtists($type, $strRouteName, $strSrchKeyword, $intGenreCountryCodeId) {
        $query = DB::table('users');
        $query->join('profiles', 'profiles.user_id', '=', 'users.id');
        $query->join('countries', 'countries.id', '=', 'profiles.country_id');
        $query->where('users.user_type_id', 1);
        $query->where('users.status', 1);
        if ($strRouteName == 'genreArtists' && !empty($intGenreCountryCodeId)) {
            $query->whereRaw("FIND_IN_SET($intGenreCountryCodeId,profiles.genres)");
        }
        if ($strRouteName == 'countryArtists' && !empty($intGenreCountryCodeId)) {
            $query->where('countries.new_country_code', $intGenreCountryCodeId);
        }
        if (!empty($strSrchKeyword)) {
            $query->whereRaw("(users.username LIKE '%" . $strSrchKeyword . "%' OR profiles.name LIKE '%" . $strSrchKeyword . "%')");
        }
        $query->select('countries.id as countryID', 'profiles.id as profileID', 'users.id as userID', 'countries.country_name', 'countries.country_code','profiles.profile_pic','users.username','profiles.name');
        
        if ($type == 'featured') {
            return $query->where('users.is_featured', 1)->orderBy('users.updated_at', 'DESC')->take(100)->get();
        }
        if ($type == 'latest') {
            return  $query->orderBy('users.id', 'DESC')->take(100)->get();
        }
        if ($type == 'trending') {
            return $query->orderBy('users.total_hits', 'DESC')->take(100)->get();
        }
    }
}
