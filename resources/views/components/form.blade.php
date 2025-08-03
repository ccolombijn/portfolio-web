<form method="POST" class="form" action="{{ $action }}">
    @csrf
    @foreach($fields as $name => $type)
        <p class="form__{{$name}}">
            <label for="{{$name}}">{{ isset($contentData['label'][$name]) ? $contentData['label'][$name] : $name }}</label>
            @if($type === 'textarea')
                <textarea name="{{$name}}" id="message"></textarea>
            @else
                <input type="{{$type}}" @if(isset($class))class="{{$class}}"@endif name="{{$name}}" id="{{$name}}" />
            @endif
        </p>
    @endforeach
    <button class="btn btn--primary form__submit" type="submit">{{$button}}</button>
</form>