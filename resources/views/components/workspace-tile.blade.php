@props(['href', 'title', 'caption', 'action', 'tone' => 'indigo', 'icon' => '+'])
<a class="workspace-tile tile-{{ $tone }}" href="{{ $href }}">
    <span class="tile-topline"><span class="tile-mark" aria-hidden="true">{{ $icon }}</span><span class="tile-arrow" aria-hidden="true">&#8599;</span></span>
    <span class="tile-name">{{ $title }}</span><span class="tile-caption">{{ $caption }}</span>
    <span class="tile-open">{{ $action }} <span aria-hidden="true">&#8599;</span></span>
</a>
