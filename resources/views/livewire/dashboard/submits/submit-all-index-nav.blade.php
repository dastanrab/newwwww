<div>
    <div class="cr-nav">
        <nav>
            <ul class="nav" role="tablist">
                <li>
                    <a href="#" class="@empty($status)) {{'active'}} @endempty" wire:click.prevent="filterStatus('')">
                        <span>در انتظار <small>({{$this->pendingCount()}})</small></span>
                    </a>
                </li>
                <li>
                    <a href="#" class="@if($status == 'active') {{'active'}} @endif" wire:click.prevent="filterStatus('active')">
                        <span>فعال <small>({{$this->activeCount()}})</small></span>
                    </a>
                </li>
                <li>
                    <a href="#" class="@if($status == 'tomorrow') {{'active'}} @endif" wire:click.prevent="filterStatus('tomorrow')">
                        <span>آتی<small>({{$this->tomorrowCount()}})</small></span>
                    </a>
                </li>
                <li>
                    <a href="#" class="@if($status == 'done') {{'active'}} @endif" wire:click.prevent="filterStatus('done')">
                        <span>انجام شده<small>({{$this->doneCount()}})</small></span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <div class="cr-time">
        <nav>
            <ul class="nav" role="tablist">
                @php( $fn = !empty($status) ? "{$status}Count" : "pendingCount")
                <li>
                    <a href="#" class="@empty($time) {{'active'}} @endempty" wire:click.prevent="filterTime('')">
                        <span>همه<small>{{$this->$fn()}}</small></span>
                    </a>
                </li>
                <li>
                    <a href="#" class="@if($time == '9') {{'active'}} @endempty" wire:click.prevent="filterTime('9')">
                        <span>9-12<small>{{$this->$fn(9)}}</small></span>
                    </a>
                </li>
                <li>
                    <a href="#" class="@if($time == '11') {{'active'}} @endempty" wire:click.prevent="filterTime('11')">
                        <span>11-14 <small>{{$this->$fn(11)}}</small></span>
                    </a>
                </li>
                <li>
                    <a href="#" class="@if($time == '13') {{'active'}} @endempty" wire:click.prevent="filterTime('13')">
                        <span>13-16<small>{{$this->$fn(13)}}</small></span>
                    </a>
                </li>
                <li>
                    <a href="#" class="@if($time == '15') {{'active'}} @endempty" wire:click.prevent="filterTime('15')">
                        <span>15-18<small>{{$this->$fn(15)}}</small></span>
                    </a>
                </li>
                <li>
                    <a href="#" class="@if($time == '17') {{'active'}} @endempty" wire:click.prevent="filterTime('17')">
                        <span>17-20<small>{{$this->$fn(17)}}</small></span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
