<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>PDS Preview</title>
    <style>
        /* Keep CSS simple for DOMPDF compatibility */
        body { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size:12px; color:#222; }
        .header { text-align:center; margin-bottom:10px; }
        .logo { width:80px; height:auto; }
        .seal { width:60px; height:auto; }
        .section { margin-bottom:12px; }
        table { width:100%; border-collapse:collapse; }
        td, th { padding:6px; border:1px solid #ddd; vertical-align:top; }
        .small { font-size:11px; color:#555; }
        img.embedded { max-width:200px; max-height:160px; display:block; }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($logos['logo']))
            <img class="logo" src="{{ $logos['logo'] }}" alt="logo">
        @endif
        <h2>Personal Data Sheet - Preview</h2>
        @if(!empty($logos['seal']))
            <img class="seal" src="{{ $logos['seal'] }}" alt="seal">
        @endif
    </div>

    <div class="section">
        <table>
            <tr>
                <th>User</th>
                <td>
                    <strong>{{ $user->name ?? ($user->fullname ?? 'Unknown') }}</strong><br>
                    <span class="small">{{ $user->email ?? '' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Records</h3>
        @forelse($records as $rec)
            <div style="margin-bottom:8px;">
                <div class="small">Model: {{ $rec['model'] }}</div>
                <table>
                    @foreach($rec['data'] as $key => $value)
                        @if($key === '_embedded_images')
                            @continue
                        @endif
                        <tr>
                            <th style="width:25%">{{ $key }}</th>
                            <td>
                                @if(isset($rec['data']['_embedded_images'][$key]))
                                    <img class="embedded" src="{{ $rec['data']['_embedded_images'][$key] }}" alt="image">
                                @else
                                    {{ is_array($value) ? json_encode($value) : $value }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if(!empty($rec['data']['_embedded_images']))
                        <tr>
                            <th>Other Images</th>
                            <td>
                                @foreach($rec['data']['_embedded_images'] as $k => $img)
                                    <div style="margin-bottom:6px;">
                                        <div class="small">{{ $k }}</div>
                                        <img class="embedded" src="{{ $img }}" alt="image">
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
        @empty
            <div class="small">No records found.</div>
        @endforelse
    </div>

    <div class="small" style="position:fixed; bottom:10px; left:10px;">
        Generated on {{ \Carbon\Carbon::now()->toDateTimeString() }}
    </div>
</body>
</html>
