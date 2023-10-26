<?php
/*
Plugin Name: KKG Music Plugin
Description: This is my first plugin! It used to create a music link!
Author: Karthigesh
*/
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class kkgmusic_music {

    private $musicId = 0;

    public function setMusicId( $id = 0 ) {
        $this->musicId = $id;
    }

    public function getSingleMusic() {
        global $wpdb;
        return $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM '.$wpdb->base_prefix.KKG_MUSIC_TABLE.' WHERE sub_id = %d', array( $this->musicId )
            ),
            ARRAY_A );
        }

        public function getAllMusic() {
            global $wpdb;
            return $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT * FROM '.$wpdb->base_prefix.KKG_MUSIC_TABLE.' WHERE mtype IN (%d,%d);',array( 1,2)
                ),
                ARRAY_A );
            }

            public function deleteMusic() {
                global $wpdb;
                return $wpdb->query(
                    $wpdb->prepare(
                        'DELETE FROM '.$wpdb->base_prefix.KKG_MUSIC_TABLE.' WHERE sub_id = %d;',
                        array( $this->musicId )
                    )
                );
            }
        }