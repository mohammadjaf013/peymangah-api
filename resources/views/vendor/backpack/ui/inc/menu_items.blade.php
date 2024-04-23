{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-item title="مدیران" icon="la la-question" :link="backpack_url('admin-model')" />
<x-backpack::menu-item title="کابران" icon="la la-question" :link="backpack_url('users')" />
<x-backpack::menu-item title="تراکنش ها" icon="la la-question" :link="backpack_url('payment-log')" />

<x-backpack::menu-item title=" قراردادها" icon="la la-question" :link="backpack_url('contracts')" />
