<div>
    @if($log->action === 'updated' && $log->old_values && $log->new_values)
        @foreach($log->new_values as $field => $newValue)
            @if(array_key_exists($field, $log->old_values) && $log->old_values[$field] !== $newValue)
                <div class="log-change">
                    <strong>{{ $field }}:</strong>
                    <span class="old">{{ $log->old_values[$field] ?? '—' }}</span>
                    →
                    <span class="new">{{ $newValue }}</span>
                </div>
            @endif
        @endforeach
    @elseif($log->action === 'created')
        <em>Новая запись</em>
    @elseif($log->action === 'deleted')
        <em>Запись удалена</em>
    @endif
</div>
