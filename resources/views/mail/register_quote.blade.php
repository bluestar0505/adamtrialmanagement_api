<x-mail::message>
株式会社アダムトライアルマネジメント<br/>
{{ $buyer->name }}様<br/>

入札システムより見積回答が届きました。<br/>
内容をご確認の上、採択通知をお願い申し上げます。<br/><br/>

案件管理No.　　　{{ $product->management_no }}<br/>
案件名　　　　　  {{ $product->product_name }}<br/><br/>

サプライヤー　　 {{ $supplier->company_name }}<br/>
回答納期　　　　  {{ \Carbon\Carbon::parse($product->delivery_date)->format('Y/m/d') }}<br/>
総額　　　　　　  ¥{{ number_format($quote->total_amount) }}<br/><br/><br/>

回答は下記URLよりご確認お願いします。<br/>
<a href="{{ $url }}">{{ $url }}</a><br/><br/>

株式会社アダムトライアルマネジメント<br/>
購買部<br/>
</x-mail::message>
