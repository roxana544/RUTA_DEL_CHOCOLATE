@props(['head'])
<div class="card overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="text-xs uppercase tracking-wider text-cream-100/50">
            <tr>@foreach ($head as $h)<th class="px-4 py-3 font-semibold">{{ $h }}</th>@endforeach</tr>
        </thead>
        <tbody class="divide-y divide-cream-100/10">{{ $slot }}</tbody>
    </table>
</div>
