<div>
    <!-- Date Picker Form -->
    <form method="GET" action="{{ route('equipment-monitorings.index') }}">
        <label for="monitoring_date" class="block text-sm font-medium text-gray-700">Filter by Monitoring Date</label>
        <input type="date" id="monitoring_date" name="monitoring_date" value="{{ request('monitoring_date') }}" class="mt-1 block w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-md">Filter</button>
    </form>

    @if($monitorings->isEmpty())
        <p>No monitoring records found for this equipment.</p>
    @else
        <table class="min-w-full divide-y divide-gray-200 mt-4">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monitored By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monitored Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facility</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($monitorings as $monitoring)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $monitoring->user->name ?? 'Unknown' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($monitoring->monitored_date)->format('F d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $monitoring->status ?? 'Unknown' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $monitoring->facility->name ?? 'Unknown' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $monitoring->remarks }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
