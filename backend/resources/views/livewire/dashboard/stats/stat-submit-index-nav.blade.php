<div>
    <div class="cr-nav">
        <nav>
            <ul class="nav" role="tablist">
                <li>
                    <a href="#" class="@empty($status)) {{'active'}} @endempty" wire:click.prevent="filterStatus('')">
                        <span>همه <small></small></span>
                    </a>
                </li>
                <li>
                    <a href="#" class="@if($status == 'pending')) {{'active'}} @endempty" wire:click.prevent="filterStatus('pending')">
                        <span>در انتظار <small></small></span>
                    </a>
                </li>
                <li>
                    <a href="#" class="@if($status == 'AssignToCar') {{'active'}} @endif" wire:click.prevent="filterStatus('AssignToCar')">
                        <span>انتصاب به خودرو <small></small></span>
                    </a>
                </li>
                <li>
                    <a href="#" class="@if($status == 'collected') {{'active'}} @endif" wire:click.prevent="filterStatus('collected')">
                        <span>جمع آوری شده<small></small></span>
                    </a>
                </li>
                <li>
                    <a href="#" class="@if($status == 'cancelByUser') {{'active'}} @endif" wire:click.prevent="filterStatus('cancelByUser')">
                        <span>لغو شده توسط کاربر<small></small></span>
                    </a>
                </li>
                <li>
                    <a href="#" class="@if($status == 'cancelByOperator') {{'active'}} @endif" wire:click.prevent="filterStatus('cancelByOperator')">
                        <span>لغو شده توسط اپراتور<small></small></span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
