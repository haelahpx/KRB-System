<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Agent Report</title>

    <style>
        /* Page reset for full-bleed background */
        @page {
            margin: 0;
        }

        /* Base content margins */
        body {
            margin: 20px;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        /* Background (Dompdf compatible) */
        .page-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ public_path("images/bg.jpg") }}');
            background-size: cover;
            background-position: center;
            opacity: 0.35;
            filter: grayscale(100%);
            pointer-events: none;
            z-index: 0;
        }

        /* Wrap all content */
        .content {
            position: relative;
            z-index: 2;
        }

        /* ==========================
            HEADER STYLE
        ========================== */
        .pdf-header {
            background: #2b2b2b;
            color: #fff;
            border-radius: 12px;
            padding: 15px;

            border: none !important;
            border-collapse: collapse;
        }

        .pdf-header td {
            border: none !important;
            background: none !important;
        }

        .pdf-header-left,
        .pdf-header-right {
            border: none !important;
        }

        .pdf-header-title {
            font-size: 20px;
            font-weight: bold;
        }

        .pdf-header-meta {
            font-size: 11px;
            margin-top: 2px;
            opacity: 0.85;
        }

        .pdf-header-logo-box {
            background: #fff;
            border-radius: 10px;
            display: inline-block;
        }

        .pdf-header-logo {
            height: 70px;
            display: block;
        }

        /* ==========================
            AGENT BLOCK
        ========================== */
        .agent-block {
            margin-top: 18px;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 12px;
        }

        /* ==========================
            STAT CARDS
        ========================== */
        .stats-row {
            display: flex;
            gap: 12px;
            margin-top: 12px;
            margin-bottom: 8px;
        }

        .stat-card {
            flex: 1 1 0;
            background: rgba(255,255,255,0.95);
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .stat-title { font-size: 12px; font-weight: bold; margin-bottom: 6px; }
        .stat-value { font-size: 18px; font-weight: 700; color: #222; }
        .stat-sub { font-size: 11px; color: #666; margin-top: 4px; }

        /* ==========================
            TABLES
        ========================== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            background: rgba(255, 255, 255, 0.85);
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
        }

        th {
            background: #2b2b2b;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
        }

        /* ==========================
            SLA BADGES
        ========================== */
        .badge-green {
            color: #008000;
            font-weight: bold;
        }

        .badge-red {
            color: #cc0000;
            font-weight: bold;
        }

        /* page footer counter placeholder (absolute) */
        .pdf-footer-page {
            position: fixed;
            right: 20px;
            bottom: 10px;
            font-size: 10px;
            color: #444;
            z-index: 99;
        }
    </style>
</head>

<body>

    <!-- Background -->
    <div class="page-bg"></div>

    <!-- All content -->
    <div class="content">

        <!-- ==========================
             HEADER
        ========================== -->
        <table class="pdf-header" width="100%">
            <tr>
                <!-- LEFT -->
                <td class="pdf-header-left">
                    <div class="pdf-header-title">Agent Report</div>
                    <div class="pdf-header-meta">
                        Generated: {{ $generatedAt->format('Y-m-d H:i') }}
                    </div>
                </td>

                <!-- RIGHT -->
                <td class="pdf-header-right" align="right">
                    <span class="pdf-header-logo-box">
                        <img src="{{ $company_logo }}" class="pdf-header-logo" alt="Logo">
                    </span>
                </td>
            </tr>
        </table>
        <!-- ==========================
             STATS CARDS
        ========================== -->

        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-title">AHT (Average Handling Time)</div>
                <div class="stat-value">
                    @if(!empty($ahtSummary['overall_avg']))
                        {{ $ahtSummary['overall_avg'] }} h
                    @else
                        -
                    @endif
                </div>
                <div class="stat-sub">Across {{ $ahtSummary['overall_count'] ?? 0 }} resolved/closed tickets</div>

                <hr style="margin:8px 0; border:none; border-top:1px solid #eee;">

                <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
                    <div style="flex:1;">
                        <div style="font-size:11px;font-weight:700">Fastest</div>
                        @if($ahtSummary['fastest'])
                            <div style="font-size:11px">{{ $ahtSummary['fastest']['full_name'] }} — {{ $ahtSummary['fastest']['avg_hours'] }} h ({{ $ahtSummary['fastest']['count'] }} tickets)</div>
                        @else
                            <div style="font-size:11px;color:#666">-</div>
                        @endif
                    </div>

                    <div style="width:12px;">&nbsp;</div>

                    <div style="flex:1;">
                        <div style="font-size:11px;font-weight:700">Slowest</div>
                        @if($ahtSummary['slowest'])
                            <div style="font-size:11px">{{ $ahtSummary['slowest']['full_name'] }} — {{ $ahtSummary['slowest']['avg_hours'] }} h ({{ $ahtSummary['slowest']['count'] }} tickets)</div>
                        @else
                            <div style="font-size:11px;color:#666">-</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-title">Top Agents by Ticket Count</div>
                <div class="stat-sub">Top {{ $topAgents->count() }} agents</div>

                <ol style="padding-left:16px;margin:8px 0 0;">
                    @forelse($topAgents as $ta)
                        <li style="font-size:12px;margin-bottom:6px;">
                            <strong>{{ $ta->full_name }}</strong>
                            <div style="font-size:11px;color:#666;">{{ $ta->company->company_name ?? '-' }} / {{ $ta->department->department_name ?? '-' }} — {{ $stats[$ta->user_id]['Total'] ?? 0 }} tickets</div>
                        </li>
                    @empty
                        <li style="font-size:11px;color:#666">No agents</li>
                    @endforelse
                </ol>
            </div>
        </div>

        <!-- ==========================
             AGENT LOOPS
        ========================== -->
        @foreach($agents as $i => $agent)
            @php
                $agentStats = $stats[$agent->user_id] ?? [];
                $agentTickets = $allTickets->where('user_id', $agent->user_id);
            @endphp

            <div class="agent-block">
                {{ $i + 1 }}. {{ $agent->full_name }}
                — {{ $agent->company->company_name ?? '-' }}
                / {{ $agent->department->department_name ?? '-' }}
                (Total: {{ $agentStats['Total'] ?? 0 }})
            </div>

                <!-- Page counter for DomPDF: bottom-right -->
                <div class="pdf-footer-page">&nbsp;</div>

                <script type="text/php">
                    if (isset($pdf)) {
                        $font = $fontMetrics->get_font("DejaVu Sans", "normal");
                        $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
                        $size = 9;
                        $y = $pdf->get_height() - 20; // 20px above bottom
                        $x = $pdf->get_width() - 60; // place near right
                        $pdf->page_text($x, $y, $text, $font, $size, array(0,0,0));
                    }
                </script>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>SLA</th>
                        <th>Elapsed</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($agentTickets as $ticket)
                        @php
                            $sla = $ticket->sla_state ?? [];
                            $slaClass = $sla['state'] === 'expired' ? 'badge-red' : 'badge-green';
                            $slaLabel = $sla['label'] ?? '-';

                            $h = $sla['hours_elapsed'] ?? 0;
                            if ($h > 0) {
                                if ($h < 1)
                                    $elapsed = floor($h * 60) . 'm';
                                elseif ($h < 24)
                                    $elapsed = floor($h) . 'h';
                                elseif ($h < 720)
                                    $elapsed = floor($h / 24) . 'd';
                                elseif ($h < 8760)
                                    $elapsed = floor($h / 720) . 'mo';
                                else
                                    $elapsed = floor($h / 8760) . 'y';
                            } else {
                                $elapsed = '';
                            }
                        @endphp

                        <tr>
                            <td>#{{ $ticket->ticket_id }}</td>
                            <td>{{ $ticket->subject ?? 'No Subject' }}</td>
                            <td>{{ ucwords(strtolower($ticket->status)) }}</td>
                            <td>{{ ucwords(strtolower($ticket->priority)) }}</td>
                            <td class="{{ $slaClass }}">{{ $slaLabel }}</td>
                            <td>{{ $elapsed }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        @endforeach

    </div>

</body>

</html>