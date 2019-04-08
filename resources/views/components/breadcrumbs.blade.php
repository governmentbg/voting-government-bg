@if(isset($breadcrumbs) && count($breadcrumbs))
<ol class="breadcrumb">
  @php 
  $last_crumb = array_pop($breadcrumbs); 
  @endphp
  @foreach($breadcrumbs as $crumb)
  @if(isset($crumb->link))
  <li><a href="{{$crumb->link}}">{{$crumb->label}}</a></li>
  @else
  <li>{{$crumb->label}}</li>
  @endif
  @endforeach
  <li class="active">{{$last_crumb->label}}</li>
</ol>
@endif
