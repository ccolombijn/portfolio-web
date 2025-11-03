
<div id="chat" class="chat"></div>
<div class="form chat-input">
    <div class="prompt-wrapper">
        <div class="prompt" id="user-input" contenteditable="true" @if(isset($profile)) data-profile="{{ $profile }}" @endif></div>
        <button id="user-input-btn" class="prompt-btn"><i class="fa-solid fa-paper-plane"></i></button>
    </div>
</div>
<div id="chat-suggestions" class="flex flex-wrap gap-2 mt-4"></div>
