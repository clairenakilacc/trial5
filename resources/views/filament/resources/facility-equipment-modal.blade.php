<div>
    @if($equipment->isEmpty())
    <p>No equipment records found for this facility.</p>
    @else
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specifications</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($equipment as $item)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $item->unit_no }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $item->description }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $item->specifications }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $item->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4 flex justify-between items-center">
        <div class="text-sm text-gray-600">
            Showing {{ $equipment->firstItem() }} to {{ $equipment->lastItem() }} of {{ $equipment->total() }} results
        </div>
        <div class="pagination-links">
            {{ $equipment->links() }}
        </div>
    </div>
    @endif
</div>
<style>
    .pagination-links nav p {
        display: none;
    }
</style>