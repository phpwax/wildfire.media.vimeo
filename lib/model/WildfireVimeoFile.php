
<?
class WildfireVimeoFile{

  public static $hash_length = 6;
  public static $name = "Vimeo";

  /**
   * - check available space
   * - get ticket
   * - upload
   **/
  public function set($media_item){
    $config = Config::get('vimeo');
    $vimeo = new phpVimeo($config['consumer']['key'], $config['consumer']['secret'], $config['oauth']['key'], $config['oauth']['secret']);
    $file = PUBLIC_DIR.$media_item->uploaded_location;
    $size = filesize($file);
    if(($quota = $vimeo->call("videos.upload.getQuota")) && ($quota - $size)){
      $source = $vimeo->upload($file);
      $vimeo->call('vimeo.videos.setTitle', array('title' => $media_item->title, 'video_id' => $source));
      $vimeo->call('vimeo.videos.setDescription', array('description' => $media_item->content, 'video_id' => $source));
      return $media_item->update_attributes(array('status'=>1, 'source'=>$source, 'media_class'=>get_class($this), 'media_type'=>self::$name));
    }

    return false;
  }
  //should return a url to display the image
  public function get($media_item, $width=false, $return = false){
    $url = "http://vimeo.com/api/oembed.json?url=".$media_item->uploaded_location."&width=".$width;
    $curl = new WaxBackgroundCurl(array('url'=>$url));
    if($data = json_decode($curl->fetch())){
      if($return) return $data;
      $html = $data->html;
      preg_match('#src="([^>]+?)"#i', $html, $matches);
      if($matches[1]) return $matches[1];
    }
    return "";
  }

  //this will actually render the contents of the image
  public function show($media_item, $size=false){
    $data = $this->get($media_item, $size, true);
    header("Location: ".$data['source']);
  }
  //generates the tag to be displayed - return generic icon if not an image
  public function render($media_item, $size, $title="preview"){
    if($data = $this->get($media_item, $size, true)) return $data->html;
    else return "";
  }

  /**
   * seems the albumn interface on vimeo is quite broken and unused, so
   * just return all
   */
  public function sync_locations(){
    return array('1'=>array('value'=>'ALL', 'label'=>'All Videos'));
  }

  public function sync($location){
    $url = "http://vimeo.com/api/v2/".Config::get('vimeo/username')."/videos.json";
    $curl = new WaxBackgroundCurl(array('url'=>$url));
    $videos = json_decode($curl->fetch());
    $ids = array();
    $info = array();
    $class = get_class($this);
    foreach((array) $videos as $video){
      $model = new WildfireMedia;
      if($found = $model->filter("media_class", $class)->filter("source", $video->id)->first()) $found->update_attributes(array('status'=>1));
      else $found = $model->update_attributes(array('source'=>$video->id,
                                                'uploaded_location'=>$video->url,
                                                'status'=>1,
                                                'media_class'=>$class,
                                                'media_type'=>self::$name,
                                                'ext'=>"",
                                                'content'=>$video->description,
                                                'file_type'=>"video",
                                                'title'=>$video->title,
                                                'hash'=> md5($video->upload_date),
                                                'sync_location'=>$location
                                                ));

      $ids[] = $found->primval;
      $info[] = $found;
      //categorisation
      foreach(explode(",", $video->tags) as $tag){
        $model = new WildfireCategory;
        if(($tag = trim($tag)) && $tag){
          if($cat = $model->filter("title", $tag)->first()) $found->categories = $cat;
          else $found->categories = $model->update_attributes(array('title'=>$tag));
        }
      }

    }
    $media = new WildfireMedia;
    foreach($ids as $id) $media->filter("id", $id, "!=");
    foreach($media->filter("status", 1)->filter("media_class", $class)->filter("sync_location", $location)->all() as $missing) $missing->update_attributes(array('status'=>-1));
    return $info;
  }


}
?>