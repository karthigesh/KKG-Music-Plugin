<?php

/*
  Plugin Name: KKG Music Plugin
  Description: This is my first plugin! It used to create a music link!
  Author: Karthigesh
 */
// If this file is called directly, abort.
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class kkgmusic_music {

    private $musicId = 0;

    public function setMusicId($id = 0) {
        $this->musicId = $id;
    }

    public function getSingleMusic() {
        $musicList[] = get_post_meta($this->musicId,'_kkgmusic',true);
        return implode(',',$musicList);
    }

    public function getAllMusic() {
        $posts = get_posts(array(
            'post_type'   => 'kkg_musics',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids'
            )
        );
        $musicList = [];
        //loop over each post
        foreach($posts as $p){
            //get the meta you need form each post
            $musicList[] = get_post_meta($p,'_kkgmusic',true);
            //do whatever you want with it
        }
        return implode(',',$musicList);
    }
    
}