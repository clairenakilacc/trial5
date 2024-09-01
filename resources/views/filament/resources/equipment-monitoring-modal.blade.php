<div>
    @if($monitorings->isEmpty())
    <p>No monitoring records found for this equipment.</p>
    @else
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monitored By</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monitored Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($monitorings as $monitoring)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $monitoring->user->name ?? 'Unknown' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $monitoring->monitored_date }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $monitoring->remarks }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $monitoring->monitoring_status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>