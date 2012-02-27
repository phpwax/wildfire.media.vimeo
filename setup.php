<?
CMSApplication::register_module("media.vimeo", array("hidden"=>true, "plugin_name"=>"wildfire.media.vimeo", 'assets_for_cms'=>true));
WildfireMedia::$classes[] = 'WildfireVimeoFile';
WildfireMedia::$allowed['mp4'] = 'WildfireVimeoFile';
WildfireMedia::$allowed['asf'] = 'WildfireVimeoFile';
WildfireMedia::$allowed['mpeg'] = 'WildfireVimeoFile';
WildfireMedia::$allowed['avi'] = 'WildfireVimeoFile';
WildfireMedia::$allowed['wmv'] = 'WildfireVimeoFile';
?>