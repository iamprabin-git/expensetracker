<style>
    * { box-sizing: border-box; }
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 9pt;
        color: #0f172a;
        margin: 0;
        padding: 14mm 12mm;
        line-height: 1.4;
    }
    .bank-statement { width: 100%; }
    .bank-statement__brand {
        background: #0f2744;
        color: #f8fafc;
        padding: 10px 14px;
        margin-bottom: 14px;
    }
    .bank-statement__brand-tag {
        margin: 0 0 2px;
        font-size: 7pt;
        font-weight: bold;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .bank-statement__brand-name {
        margin: 0;
        font-size: 14pt;
        font-weight: bold;
    }
    .bank-statement__brand-meta {
        margin-top: 6px;
        font-size: 8pt;
        text-align: right;
    }
    .bank-statement__brand-meta p { margin: 0; }
    .bank-statement__parties {
        width: 100%;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 2px solid #0f172a;
    }
    .bank-statement__parties-table { width: 100%; border-collapse: collapse; }
    .bank-statement__parties-table td { vertical-align: top; width: 50%; }
    .bank-statement__label {
        margin: 0 0 4px;
        font-size: 7pt;
        font-weight: bold;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.08em;
    }
    .bank-statement__account-name {
        margin: 0;
        font-size: 11pt;
        font-weight: bold;
    }
    .bank-statement__account-detail {
        margin: 2px 0 0;
        font-size: 8.5pt;
        color: #64748b;
    }
    .bank-statement__meta { text-align: right; font-size: 8.5pt; }
    .bank-statement__meta p { margin: 2px 0; }
    .bank-statement__meta strong { color: #0f172a; }
    .bank-statement__summary {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 12px;
    }
    .bank-statement__summary td {
        width: 25%;
        border: 1px solid #cbd5e1;
        padding: 8px;
        text-align: center;
        background: #f8fafc;
    }
    .bank-statement__summary .sum-label {
        font-size: 7pt;
        text-transform: uppercase;
        color: #64748b;
        font-weight: bold;
    }
    .bank-statement__summary .sum-value {
        margin-top: 3px;
        font-size: 11pt;
        font-weight: bold;
    }
    .bank-statement__summary .credit { color: #047857; }
    .bank-statement__summary .debit { color: #b91c1c; }
    .bank-statement__data {
        width: 100%;
        border-collapse: collapse;
    }
    .bank-statement__data th,
    .bank-statement__data td {
        border-bottom: 1px solid #e2e8f0;
        padding: 5px 6px;
        text-align: left;
        vertical-align: top;
    }
    .bank-statement__data thead th {
        border-top: 2px solid #0f172a;
        border-bottom: 1px solid #0f172a;
        background: #f1f5f9;
        font-size: 7pt;
        text-transform: uppercase;
        font-weight: bold;
    }
    .bank-statement__data tbody tr:nth-child(even) { background: #fafbfc; }
    .bank-statement__data .num { text-align: right; white-space: nowrap; }
    .bank-statement__data .desc-sub {
        display: block;
        font-size: 7.5pt;
        color: #64748b;
        margin-top: 2px;
    }
    .bank-statement__data tfoot th,
    .bank-statement__data tfoot td {
        border-top: 2px solid #0f172a;
        background: #f1f5f9;
        font-weight: bold;
        text-align: right;
    }
    .bank-statement__legal {
        margin-top: 14px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
        font-size: 7pt;
        color: #64748b;
    }
</style>
