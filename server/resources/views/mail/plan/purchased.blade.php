@component('mail::message')
# Introduction

The body of your message.

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

![SPOTFIT][logo]
[logo]: {{asset('/spotfit.svg')}}


Thanks,<br>
{{ config('app.name') }}
@endcomponent
