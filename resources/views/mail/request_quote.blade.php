<x-mail::message>
{{ $supplier->company_name }}<br/>
{{ $supplier->contact_name }}様<br/>

いつもお世話になっております。<br/>
入札システムより新規見積依頼が届きました。<br/>
内容をご確認の上、期日までにご回答お願い申し上げます。<br/><br/>

案件管理No.　　　{{ $product->management_no }}<br/>
案件名　　　　　  {{ $product->product_name }}<br/>
希望納期　　　　  {{ \Carbon\Carbon::parse($product->desired_delivery_date)->format('Y/m/d') }}<br/>
回答期日　　　　  {{ \Carbon\Carbon::parse($product->reply_due_date)->format('Y/m/d') }}<br/><br/><br/>

回答は下記URLよりお願いします。<br/>
<a href="{{ $url }}">{{ $url }}</a><br/><br/>


株式会社アダムトライアルマネジメント<br/>
購買部<br/>
</x-mail::message>
