<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            <table class="cr-table table" >
                <thead>
                <tr>
                    <th>تاریخ</th>
                    <th>موجودی کیف پول آپ (تومان)</th>
                    <th>برداشت آپ (تومان)</th>
                    <th>واریز آپ (تومان)</th>
                    <th>پاداش اولین درخواست (تومان)</th>
                    <th>پاداش معرفین (تومان)</th>
                    <th>واریزی های مجموعه (تومان)</th>
                    <th>برداشت های مجموعه (تومان)</th>
                    <th>موجودی کیف پول کاربران (تومان)</th>
                    <th>واریزهای کیف پول بازیست (تومان)</th>
                </tr>
                </thead>
                <tbody>
                    <tr wire:key="1">
                        <td>{{ $date }}</td>
                        <td dir="ltr">{{ $app_total }}</td>
                        <td>{{ $app_withdraw }}</td>
                        <td>{{ $aap_deposit }}</td>
                        <td>{{ number_format($rewards_first) }}</td>
                        <td>{{ number_format($rewards_ref) }}</td>
                        <td dir="ltr">{{ number_format($office_deposit) }}</td>
                        <td dir="ltr">{{ number_format($office_withdraw) }}</td>
                        <td dir="ltr">{{ number_format($bazist_wallet) }}</td>
                        <td>{{ $cashout }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="cr-card-footer">
    </div>
    @script
    <script>

    </script>
    @endscript
</div>
