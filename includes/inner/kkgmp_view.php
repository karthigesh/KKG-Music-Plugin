<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
$musicContent = kkgmusic_getsingle(filter_input(INPUT_GET, 'element', FILTER_SANITIZE_NUMBER_INT));
$viewMusicTitle = $musicContent['music_title'];
$viewMusicPlayer = plugins_url('imgs/dvd.png', __FILE__);
$viewMusicContent = $musicContent['sub_musicurl'];
?>
<div class = 'wrap'>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <h1><?php echo esc_html('Welcome to KKG Music App!'); ?></h1>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-4'></div>
        <div class = 'col-md-4' style="overflow: hidden;padding: 0;">
            <div class = 'maine'>
                <div class="player">
                    <div class="details">
                        <div class="track-art"></div>
                        <div class="track-name"><?php echo esc_html('Track Name'); ?></div>
                    </div>
                    <div class="buttons">
                        <!-- <div class="prev-track" onclick="prevTrack()"><i class="fa fa-step-backward fa-2x"></i></div> -->
                        <div class="playpause-track" onclick="playpauseTrack()"><i class="fa fa-play-circle fa-5x"></i></div>
                        <!-- <div class="next-track" onclick="nextTrack()"><i class="fa fa-step-forward fa-2x"></i></div> -->
                    </div>
                    <div class="slider_container">
                        <div class="current-time">00:00</div>
                        <input type="range" min="1" max="100" value="0" class="seek_slider" onchange="seekTo()">
                        <div class="total-duration">00:00</div>
                    </div>
                    <div class="slider_container">
                        <i class="fa fa-volume-down"></i>
                        <input type="range" min="1" max="100" value="99" class="volume_slider" onchange="setVolume()">
                        <i class="fa fa-volume-up"></i>
                    </div>
                </div>
                <script>
                    let player = document.querySelector(".player");
                    let track_art = document.querySelector(".track-art");
                    let track_name = document.querySelector(".track-name");

                    let playpause_btn = document.querySelector(".playpause-track");
                    let next_btn = document.querySelector(".next-track");
                    let prev_btn = document.querySelector(".prev-track");

                    let seek_slider = document.querySelector(".seek_slider");
                    let volume_slider = document.querySelector(".volume_slider");
                    let curr_time = document.querySelector(".current-time");
                    let total_duration = document.querySelector(".total-duration");

                    //let animateSect = document.querySelector(".animatesect");

                    let track_index = 0;
                    let isPlaying = false;
                    let updateTimer;

                    // Create new audio element
                    let curr_track = document.createElement('audio');

                    // Define the tracks that have to be played
                    let track_list = [
                        {
                            name: "<?php echo esc_html($viewMusicTitle); ?>",
                            image: "<?php echo $viewMusicPlayer; ?>",
                            path: "<?php echo esc_url($viewMusicContent); ?>"
                        }
                    ];

                    function loadTrack(track_index) {
                        clearInterval(updateTimer);
                        resetValues();

                        // Load a new track
                        curr_track.src = track_list[track_index].path;
                        curr_track.load();

                        // Update details of the track
                        track_art.style.backgroundImage = "url(" + track_list[track_index].image + ")";
                        track_name.textContent = track_list[track_index].name;

                        // Set an interval of 1000 milliseconds for updating the seek slider
                        updateTimer = setInterval(seekUpdate, 1000);

                        // Move to the next track if the current one finishes playing
                        curr_track.addEventListener("ended", nextTrack);

                        // Apply a random background color
                        random_bg_color();
                        random_bg_flow();
                    }

                    function random_bg_color() {
                        // Get a random number between 64 to 256 (for getting lighter colors)
                        let red = Math.floor(Math.random() * 256) + 64;
                        let green = Math.floor(Math.random() * 256) + 64;
                        let blue = Math.floor(Math.random() * 256) + 64;

                        // Construct a color withe the given values
                        let bgColor = "rgb(" + red + "," + green + "," + blue + ")";
                        // Set the background to that color
                        document.querySelector(".player").style.background = bgColor;
                    }

                    function random_bg_flow() {
                        // Get a random number between 64 to 256 (for getting lighter colors)
                        let red = Math.floor(Math.random() * 256) + 0;
                        let green = Math.floor(Math.random() * 256) + 0;
                        let blue = Math.floor(Math.random() * 256) + 0;

                        // Construct a color withe the given values
                        let card__line_left = "linear-gradient(to bottom, rgb(" + red + "," + green + "," + blue + "), transparent)";
                        let card__line_right = "linear-gradient(to bottom, transparent, rgb(" + red + "," + green + "," + blue + "))";
                        let card__line_top = "linear-gradient(to right, transparent, rgb(" + red + "," + green + "," + blue + "))";
                        let card__line_bottom = "linear-gradient(to right, rgb(" + red + "," + green + "," + blue + "),transparent)";

                        // Set the background to that color
                        document.querySelector(".card__line_left").style.background = card__line_left;
                        document.querySelector(".card__line_right").style.background = card__line_right;
                        document.querySelector(".card__line_top").style.background = card__line_top;
                        document.querySelector(".card__line_bottom").style.background = card__line_bottom;
                    }

                    // Reset Values
                    function resetValues() {
                        curr_time.textContent = "00:00";
                        total_duration.textContent = "00:00";
                        seek_slider.value = 0;
                    }

                    function playpauseTrack() {
                        if (!isPlaying)
                            playTrack();
                        else
                            pauseTrack();
                    }

                    function playTrack() {
                        curr_track.play();
                        isPlaying = true;

                        // Replace icon with the pause icon
                        playpause_btn.innerHTML = '<i class="fa fa-pause-circle fa-5x"></i>';
                        //animateSect.style.display = 'block';
                        track_art.classList.add("rounding");
                    }

                    function pauseTrack() {
                        curr_track.pause();
                        isPlaying = false;

                        // Replace icon with the play icon
                        playpause_btn.innerHTML = '<i class="fa fa-play-circle fa-5x"></i>';
                        //animateSect.style.display = 'none';
                        track_art.classList.remove("rounding");
                    }

                    function nextTrack() {
                        if (track_index < track_list.length - 1)
                            track_index += 1;
                        else
                            track_index = 0;
                        loadTrack(track_index);
                        playTrack();
                    }

                    function prevTrack() {
                        if (track_index > 0)
                            track_index -= 1;
                        else
                            track_index = track_list.length;
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
                            if (currentSeconds < 10) {
                                currentSeconds = "0" + currentSeconds;
                            }
                            if (durationSeconds < 10) {
                                durationSeconds = "0" + durationSeconds;
                            }
                            if (currentMinutes < 10) {
                                currentMinutes = "0" + currentMinutes;
                            }
                            if (durationMinutes < 10) {
                                durationMinutes = "0" + durationMinutes;
                            }

                            curr_time.textContent = currentMinutes + ":" + currentSeconds;
                            total_duration.textContent = durationMinutes + ":" + durationSeconds;
                        }
                    }

                    // Load the first track in the tracklist
                    loadTrack(track_index);
                </script>
            </div>
        </div>
        <div class = 'col-md-4'></div>
    </div><!-- / row -->
</div><!-- / wrap -->
