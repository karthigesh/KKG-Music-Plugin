<?php
$musicContent = new kkgmusic_music();
$musicList = $musicContent->getAllMusic();
?>
<audio controls>
  <source src="https://ia800905.us.archive.org/19/items/FREE_background_music_dhalius/backsound.mp3"  type="audio/mp3">
</audio> 
<div style="width: 50px; height: 50px;"></div>
<div class="audio-player">
  <div class="timeline">
    <div class="progress"></div>
  </div>
  <div class="controls">
    <div class="play-container">
      <div class="toggle-play play">
    </div>
    </div>
    <div class="time">
      <div class="current">0:00</div>
      <div class="divider">/</div>
      <div class="length"></div>
    </div>
    <div class="name">Music Song</div>
    <div class="volume-container">
      <div class="volume-button">
        <div class="volume icono-volumeMedium"></div>
      </div>
      
      <div class="volume-slider">
        <div class="volume-percentage"></div>
      </div>
    </div>
  </div>
</div>
    
</div>