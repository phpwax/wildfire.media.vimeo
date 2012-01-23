
<?
class WildfireVimeoFile{

  public static $hash_length = 6;
  public static $name = "Vimeo";

  /**
   *
   **/
  public function set($media_item){
    return false;
  }
  //should return a url to display the image
  public function get($media_item, $width=false, $return_obj = false){

  }

  //this will actually render the contents of the image
  public function show($media_item, $size=false){

  }
  //generates the tag to be displayed - return generic icon if not an image
  public function render($media_item, $size, $title="preview"){


  }

  /**
   * have to find all photosets & galleries
   */
  public function sync_locations(){

  }

  public function sync($location){

  }


}
?>