<x-mail::message>
{{ $supplier->company_name }}<br/>
{{ $supplier->contact_name }}様<br/>

入札システムよりサプライヤー登録通知が届きました。<br/>
入札システムにログインして見積依頼にご回答お願い申し上げます。<br/><br/>

ログインID.　　　{{ $supplier->contact_email }}<br/>
パスワード　　　{{ $password }}<br/><br/>


下記URLでログインしてください。<br/>
<a href="{{ $url }}">{{ $url }}</a><br/><br/>

株式会社アダムトライアルマネジメント<br/>
購買部<br/>
</x-mail::message>
