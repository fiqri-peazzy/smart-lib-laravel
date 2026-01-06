<div class="space-y-4 py-4">
    @if($transactions->isEmpty())
        <div class="text-center py-4 text-gray-500">
            Tidak ada riwayat transaksi gateway.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="pb-2 font-semibold">Tgl Transaksi</th>
                        <th class="pb-2 font-semibold">Order ID</th>
                        <th class="pb-2 font-semibold">Metode</th>
                        <th class="pb-2 font-semibold text-right">Jumlah</th>
                        <th class="pb-2 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($transactions as $transaction)
                        <tr>
                            <td class="py-2">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                            <td class="py-2">{{ $transaction->gateway_order_id }}</td>
                            <td class="py-2">
                                {{ strtoupper($transaction->payment_method) }}
                                @if($transaction->payment_channel)
                                    <span class="text-xs text-gray-500">({{ $transaction->payment_channel }})</span>
                                @endif
                            </td>
                            <td class="py-2 text-right">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td class="py-2 text-center">
                                <span @class([
                                    'px-2 py-0.5 rounded-full text-xs font-medium',
                                    'bg-yellow-100 text-yellow-800' => $transaction->status === 'pending',
                                    'bg-green-100 text-green-800' => $transaction->status === 'success',
                                    'bg-red-100 text-red-800' => $transaction->status === 'failed',
                                    'bg-gray-100 text-gray-800' => $transaction->status === 'expired',
                                ])>
                                    {{ strtoupper($transaction->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
