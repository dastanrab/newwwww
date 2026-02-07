@php use App\Livewire\Dashboard\Stats\StatSubmitIndex; @endphp
<div>
    <div class="cr-nav">
        <nav>
            <ul class="nav" role="tablist">
                @if(Gate::allows('stat_daily_index',StatSubmitIndex::class))
                    <li>
                        <a href="{{route('d.stats.daily')}}">
                            <span>روزانه</span>
                        </a>
                    </li>
                @endif
                @if(Gate::allows('stat_monthly_index',StatSubmitIndex::class))
                    <li>
                        <a href="{{route('d.stats.monthly')}}">
                            <span>ماهانه</span>
                        </a>
                    </li>
                @endif
                @if(Gate::allows('stat_total_index',StatSubmitIndex::class))
                    <li>
                        <a href="{{route('d.stats.total')}}">
                            <span>کل</span>
                        </a>
                    </li>
                @endif
                @if(Gate::allows('stat_area_index',StatSubmitIndex::class))
                    <li>
                        <a href="{{route('d.stats.area')}}" class="active">
                            <span>مناطق</span>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</div>
