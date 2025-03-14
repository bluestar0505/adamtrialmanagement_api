<x-mail::message>
{{ $supplier->company_name }}<br/>
{{ $supplier->contact_name }}様<br/>

いつもお世話になっております。<br/>
入札システムより差し戻し結果が届きました。<br/>
内容をご確認の上、回答期日までに回答を再度お願い申し上げます。<br/><br/>

案件管理No.　　　{{ $product->management_no }}<br/>
案件名　　　　　  {{ $product->product_name }}<br/><br/>

回答納期　　　　  {{ \Carbon\Carbon::parse($product->delivery_date)->format('Y/m/d') }}<br/>
総額　　　　　　  ¥{{ number_format($quote->total_amount) }}<br/><br/><br/>

回答は下記URLよりお願いします。<br/>
<a href="{{ $url }}">{{ $url }}</a><br/><br/>

株式会社アダムトライアルマネジメント<br/>
購買部<br/>
</x-mail::message>
