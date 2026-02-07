<div class="cr-card" id="paginated-orders">
    <div class="cr-card-header">
        <div class="cr-title mb-0">
            <div>
                <strong>لیست سفارش ها</strong>
            </div>
        </div>
    </div>
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            <table class="cr-table table">
                <thead>
                <tr>
                    <th>تاریخ</th>
                    <th>محصول</th>
                    <th>قیمت <small>(تومان)</small></th>
                </tr>
                </thead>
                @if($this->orders->count())
                <tbody>
                @foreach($this->orders as $order)
                    <tr>
                        <td>{{ \Verta::instance($order->created_at)->format('Y/n/j H:i') }}</td>
                        <td>
                            @if ($order->package_code)
                                شارژ اینترنت
                            @else
                                شارژ موبایل
                            @endif
                        </td>
                        <td><strong>{{ number_format($order->amount/10) }}</strong></td>
                    </tr>
                @endforeach
                </tbody>
                @endif
            </table>
        </div>
    </div>
    <div class="cr-card-footer">
        {{$this->orders->links(data: ['scrollTo' => '#paginated-orders'])}}
    </div>
</div>
