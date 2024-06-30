<html lang="fa" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Peymangah</title>
    <style>
{{--        @font-face {--}}
{{--            font-family: 'Vazir';--}}
{{--            font-style: normal;--}}
{{--            font-weight: normal;--}}
{{--            src: url('{{ asset("/storage/fonts/Vazir-Regular.ttf") }}') format('truetype');--}}
{{--        }--}}
{{--        body {--}}
{{--            font-family: 'Vazir', sans-serif;--}}
{{--            text-align: right;--}}
{{--            direction: rtl;--}}
{{--        }--}}
{{--        .font-style{--}}
{{--            font-family: 'Vazir', sans-serif;--}}
{{--            text-align: right;--}}
{{--            direction: rtl;--}}
{{--        }--}}
                  .page-border{
    width: 100%;
    height: 100%;
    border:4px double black;
}
@page {
    header: page-header;
    footer: page-footer;

    border: 2mm solid #000000;
    padding: 10mm;
}
body {
    margin: 0;
    padding: 0;
 }
    </style>


</head>
<body dir="rtl">


<htmlpageheader name="page-header">

    <table width="100%" dir="rtl" style="border-bottom:1px solid black;padding-bottom: 15px;">
        <tr>
            <td width="33%" style="text-align: right;">

            </td>

            <td width="33%" align="center" style="; ;">
                <div style="width: 100%;text-align: center;font-size: 18px;" >
                    {{$contract->title}}
                </div>
            </td>
            <td width="33%" style="text-align: left;">
                <span style="font-size: 10px;">
                    تاریخ قرارداد:
                   @if($isSigned)
                        {{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($date))->format('%A, %d %B %Y')}}
                    @endif
                </span>
                <div style="font-size: 10px;">
                    <div style="padding-top: 5px"></div>
                     شماره قرارداد: {{$contract->id}}
                </div>
                <div style="font-size: 10px;">
                    <div style="padding-top: 5px"></div>
                     پیوست : {!! ( count($contract->attaches) ) ? count($contract->attaches) . " فایل " : "ندارد"  !!}
                </div>
            </td>
        </tr>
    </table>

</htmlpageheader>

<htmlpagefooter name="page-footer" >
    <table width="100%" dir="rtl" style="border-top:1px solid black;padding-top: 10px;">
        <tr>
            <td width="33%" style="text-align: right;">
                <div style="width: 100%;text-align: center;font-size: 10px;" >
                    این قرارداد در پلتفرم
                    <a href="https://peymangah.com" target="_blank" style="text-decoration: none;color: black">
                        پیمانگاه
                    </a>
                    ایجاد شده است.
                </div>
            </td>

            <td width="33%" align="center" style="; ;">
                {PAGENO}/{nbpg}
            </td>
            <td width="33%" style="text-align: left;">
                <a href="https://peymangah.com" target="_blank" style="text-decoration: none;font-size: 10px">
                www.peymangah.com
                </a>
{{--                <span style="; ;">{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($contract->created_at))->format('%A, %d %B %y')}}</span>--}}
            </td>
        </tr>
    </table>
</htmlpagefooter>


<div style="direction: rtl; ; padding:10px; " class="">
    @if($contract->users->count() > 0)
        <div class="font-style">
            اين قرارداد فی مابين
            @foreach($contract->users as $index => $user)
                <div class="font-style">
                    {{ $user->first_name }} {{ $user->last_name }}
                    به کد ملی {{ $user->ssn }}
                    به نشانی {{ $user->address }}
                    تلفن {{ $user->phone }}, {{ $user->mobile }}
                    که از این پس {{ $user->title }} نامیده میشود؛
                    @if($index + 1 < $contract->users->count())
                        و از طرف دیگر
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if($contract->items->count() > 0)
        @foreach($contract->items as $index => $item)
            <div class="font-style">
                <h2 class="font-style" style="font-size: 15px;">{{ $index + 1 }}-{{ $item->title }}:</h2>
                @foreach($item->contents as $indec => $content)
                    <div class="font-style">
                        {{ $index + 1 }}.{{ $indec + 1 }}- {{ $content->content }}
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif



    <div style="margin-top:60px;display: inline-block">
        @foreach($contract->users as $index => $user)
            <div style="width: 150px;float: right;padding-left: 50px;">

                <div>
                    {{ $user->first_name }} {{ $user->last_name }}
                </div>
             @if($user->is_signed)  <div>
                   <img src="{{$user->fileUrl("signature")}}" />
               </div>
                @endif
            </div>
        @endforeach


    </div>


    @if(!$isSigned)
        <div style="color:red; text-align: center; font-size: 20px ; font-weight: bold">
            قرارداد نهایی نشده است.
        </div>
    @endif
</div>
</body>
</html>
