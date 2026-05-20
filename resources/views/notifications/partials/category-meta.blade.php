@php
    $category = $category ?? 'general';
    $meta = match ($category) {
        'reminder' => ['label' => 'Reminder', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
        'account' => ['label' => 'Account', 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 19.118a7.5 7.5 0 0114.998 0'],
        'membership' => ['label' => 'Membership', 'icon' => 'M16.5 6v.75a2.25 2.25 0 01-2.25 2.25h-1.5a2.25 2.25 0 01-2.25-2.25V6M4.5 19.5h15'],
        default => ['label' => 'General', 'icon' => 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    };
@endphp
