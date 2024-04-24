<html lang="fa" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Peymangah</title>
    <style>

        .page-border {
            width: 100%;
            height: 100%;
            border: 4px double black;
        }

        @page {
            header: page-header;
            footer: page-footer;

            border: 2mm solid #000000; /* اضافه کردن حاشیه به هر صفحه */
            padding: 10mm; /* اضافه کردن فاصله داخلی به محتوای داخل حاشیه */
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
                <div style="width: 100%;text-align: center;font-size: 18px;">
                    {{$receipt->title}}
                </div>
            </td>
            <td width="33%" style="text-align: left;">
                <span style="font-size: 10px;">
                    تاریخ رسید:
                    {{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($receipt->created_at))->format('%A, %d %B %Y')}}</span>
            </td>
        </tr>
    </table>

</htmlpageheader>

<htmlpagefooter name="page-footer">
    <table width="100%" dir="rtl" style="border-top:1px solid black;padding-top: 10px;">
        <tr>
            <td width="33%" style="text-align: right;">
                {{$receipt->title}}
            </td>

            <td width="33%" align="center" style="; ;">
                {PAGENO}/{nbpg}
            </td>
            <td width="33%" style="text-align: left;">
                <span
                    style="; ;">{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($receipt->created_at))->format('%A, %d %B %y')}}</span>
            </td>
        </tr>
    </table>
</htmlpagefooter>


<div style="direction: rtl; ; padding:10px; " class="">
    @if($receipt->users->count() > 0)
        <div class="font-style">
            امضا کنندگان رسید:
            @foreach($receipt->users as $index => $user)
                <div class="font-style">
                    {{ $user->first_name }} {{ $user->last_name }}
                    به کد ملی {{ $user->ssn }}
                    به نشانی {{ $user->address }}
                    تلفن {{ $user->phone }}, {{ $user->mobile }}
                    که از این پس {{ $user->title }} نامیده میشود؛
                    @if($index + 1 < $receipt->users->count())
                        و از طرف دیگر
                    @endif
                </div>
            @endforeach
        </div>
    @endif


    <div class="font-style" style="padding-top:15px ">
        {{$receipt->body}}
    </div>


    <div style="margin-top:60px;display: inline-block">
        @foreach($receipt->users as $index => $user)
            <div style="width: 150px;float: right;padding-left: 50px;">

                <div>
                    {{ $user->first_name }} {{ $user->last_name }}
                </div>
                @if($user->is_signed)
                    <div>
                        <img src="{{$user->fileUrl("signature")}}"/>
                    </div>
                @endif
            </div>
        @endforeach


    </div>
</div>
</body>
</html>
