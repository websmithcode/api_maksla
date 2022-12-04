<div>Клиент: {{$client_name}}<br>
Телефон: <a href="tel:{{$client_phone}}">{{$client_phone}}</a><br>
Email: <a href="mailto:{{$client_email}}">{{$client_email}}</a><br>
Автомобиль: {{$car}}<br>
Дата: {{$date}} в {{$time}}<br>
<p>Услуги: {{$maintenance_name}}</p>
<ul>
	@foreach($works as $work)
	<li>{{$work}}</li>
	@endforeach
</ul>
<p>Стоимость: {{$price}}</p>

@unless(empty($extra))
<hr>
<p>Дополнительная информация:</p>
<ul>
	@foreach($extra as $key => $value)
	<li>{{$key}}: {{$value}}</li>
	@endforeach
</ul>
@endunless
