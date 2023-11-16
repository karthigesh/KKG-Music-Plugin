<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$musicContent = new kkgmusic_music();
$musicList = $musicContent->getAllMusic();
$musicView = plugins_url( 'kkg_music/includes/inner/imgs/dvd.png', __FILE__ );
?>
<div class = 'maine'>
<div class="player">
  <div class="visual">
    <div class="visualization stop"></div>
  </div>
        <div class="details">     
            <div class="track-name"><?php echo esc_html( 'Track Name' );?></div>
        </div>
        <div class="buttons">
            <div class="prev-track" onclick="prevTrack()"><i class="fa fa-step-backward fa-2x"></i></div>
            <div class="playpause-track" onclick="playpauseTrack()"><i class="fa fa-play-circle fa-3x"></i></div>
            <div class="next-track" onclick="nextTrack()"><i class="fa fa-step-forward fa-2x"></i></div>
        </div>
        <div class="slider_container">
            <div class="current-time"><?php echo esc_html( '00:00' );?></div>
            <input type="range" min="1" max="100" value="0" class="seek_slider" onchange="seekTo()">
            <div class="total-duration"><?php echo esc_html( '00:00' );?></div>
        </div>
        <div class="slider_container">
            <i class="fa fa-volume-down"></i>
            <input type="range" min="1" max="100" value="99" class="volume_slider" onchange="setVolume()">
            <i class="fa fa-volume-up"></i>
        </div>
    </div>
    <?php
    $musicSects = [];
    if(isset($musicList) && (count($musicList)>0)){
      foreach($musicList as $k=>$musics){
        $musicSects[$k]['name'] = esc_html($musics['music_title']);
        $musicSects[$k]['image'] = sanitize_url($musicView, array('http', 'https'));
        $musicSects[$k]['path'] = sanitize_url($musics['sub_musicurl'], array('http', 'https'));
      }
    }
    ?>
    <script>
        let player = document.querySelector(".player");
        let track_name = document.querySelector(".track-name");
        
        let playpause_btn = document.querySelector(".playpause-track");
        let next_btn = document.querySelector(".next-track");
        let prev_btn = document.querySelector(".prev-track");
        
        let seek_slider = document.querySelector(".seek_slider");
        let volume_slider = document.querySelector(".volume_slider");
        let curr_time = document.querySelector(".current-time");
        let total_duration = document.querySelector(".total-duration");
        let visualization = document.querySelector(".visualization");
        
        // let animateSect = document.querySelector(".animatesect");
        
        let track_index = 0;
        let isPlaying = false;
        let updateTimer;
        
        // Create new audio element
        let curr_track = document.createElement('audio');
        
        // Define the tracks that have to be played
        let track_list = <?php echo wp_json_encode($musicSects);?>;
        function loadTrack(track_index) {
          clearInterval(updateTimer);
          resetValues();
        
          // Load a new track
          curr_track.src = track_list[track_index].path;
          curr_track.load();
        
          // Update details of the track
          track_name.textContent = track_list[track_index].name;
        
          // Set an interval of 1000 milliseconds for updating the seek slider
          updateTimer = setInterval(seekUpdate, 1000);
        
          // Move to the next track if the current one finishes playing
          curr_track.addEventListener("ended", nextTrack);
        
          // Apply a random background color
          random_bg_color();
        }
        
        function random_bg_color() {        
          // Get a random number between 64 to 256 (for getting lighter colors)
          let red = Math.floor(Math.random() * 256) + 64;
          let green = Math.floor(Math.random() * 256) + 64;
          let blue = Math.floor(Math.random() * 256) + 64;
        
          // Construct a color withe the given values
          let bgColor = "rgb(130, 148, 131)";
          // Set the background to that color
          document.querySelector(".player").style.background = bgColor;
        }
        
        // Reset Values
        function resetValues() {
          curr_time.textContent = "00:00";
          total_duration.textContent = "00:00";
          seek_slider.value = 0;
        }
        
        function playpauseTrack() {
          if (!isPlaying) playTrack();
          else pauseTrack();
        }        
        function playTrack() {
          curr_track.play();
          isPlaying = true;
        
          // Replace icon with the pause icon
          playpause_btn.innerHTML = '<i class="fa fa-pause-circle fa-3x"></i>';
          // animateSect.style.display = 'block';
          visualization.classList.add("start");
          visualization.classList.remove("stop");
        }
        
        function pauseTrack() {
          curr_track.pause();
          isPlaying = false;
        
          // Replace icon with the play icon
          playpause_btn.innerHTML = '<i class="fa fa-play-circle fa-3x"></i>';
          // animateSect.style.display = 'none';
          visualization.classList.add("stop");
          visualization.classList.remove("start");
          document.querySelector(".player").classList.remove("border");
        }
        
        function nextTrack() {
          if (track_index < track_list.length - 1)
            track_index += 1;
          else track_index = 0;
          loadTrack(track_index);
          playTrack();
        }
        
        function prevTrack() {
          if (track_index > 0)
            track_index -= 1;
          else track_index = track_list.length;
          loadTrack(track_index);
          playTrack();
        }
        
        function seekTo() {
          seekto = curr_track.duration * (seek_slider.value / 100);
          curr_track.currentTime = seekto;
        }
        
        function setVolume() {
          curr_track.volume = volume_slider.value / 100;
        }
        
        function seekUpdate() {
          let seekPosition = 0;
        
          // Check if the current track duration is a legible number
          if (!isNaN(curr_track.duration)) {
            seekPosition = curr_track.currentTime * (100 / curr_track.duration);
            seek_slider.value = seekPosition;
        
            // Calculate the time left and the total duration
            let currentMinutes = Math.floor(curr_track.currentTime / 60);
            let currentSeconds = Math.floor(curr_track.currentTime - currentMinutes * 60);
            let durationMinutes = Math.floor(curr_track.duration / 60);
            let durationSeconds = Math.floor(curr_track.duration - durationMinutes * 60);
        
            // Adding a zero to the single digit time values
            if (currentSeconds < 10) { currentSeconds = "0" + currentSeconds; }
            if (durationSeconds < 10) { durationSeconds = "0" + durationSeconds; }
            if (currentMinutes < 10) { currentMinutes = "0" + currentMinutes; }
            if (durationMinutes < 10) { durationMinutes = "0" + durationMinutes; }
        
            curr_time.textContent = currentMinutes + ":" + currentSeconds;
            total_duration.textContent = durationMinutes + ":" + durationSeconds;
          }
        }
        
        // Load the first track in the tracklist
        loadTrack(track_index);

    </script>
</div>