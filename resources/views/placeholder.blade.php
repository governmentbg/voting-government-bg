Action: {{dump(request()->route()->getActionMethod())}}

Data: 

@php
if(isset($votingTours))
    dd($votingTours);
    
if(isset($votingTour))
    dd($votingTour);   
@endphp


