
<div id="chat" class="chat"></div>
<div class="form chat-input"><input id="user-input" @if(isset($profile)) data-profile="{{ $profile }}" @endif/><button id="user-input-btn" class="btn btn-primary">Verzenden</button></div>
<div id="chat-suggestions" class="flex flex-wrap gap-2 mt-4"></div>
