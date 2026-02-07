<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FeedBack</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color:#111; font-size:12px; margin: 120px 30px 50px 30px; }
        .container { max-width: 900px; margin: auto; }
        .header { position: fixed; top: 0; left: 0; right: 0; background: white; z-index: 1000; text-align:center; padding:6px; }
        .logo { width:85px; height:auto; display:block; margin:0 auto 4px; }
        .title { font-weight:700; font-size:14px; text-transform:uppercase; font-family: serif; }
        .subtitle { font-size:10px; text-transform:uppercase; }
        .section { margin-top: 12px; }
        .instruction { border: 1px solid #000; padding: 6px; font-weight: bold; margin-top: 10px; }
        .row { display: flex; gap: 15px; margin-bottom: 5px; flex-wrap: wrap; }
        .field { flex: 1; min-width: 200px; }
        .line { border-bottom: 1px solid #000; display: inline-block; width: 100%; height: 12px; }
        .checkbox-group label { display: block; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { border: 1px solid #000; padding: 6px; text-align: center; vertical-align: middle; }
        table th:first-child, table td:first-child { text-align: left; width: 45%; }
        .emoji { font-size: 16px; display: block; margin-bottom: 2px; }
        .footer { margin-top: 15px; }
        .thank-you { text-align: center; font-weight: bold; margin-top: 20px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; background: white; z-index: 1000; padding: 6px; text-align: center; border-top: 1px solid #000; }
        .page-number::after { content: "Page " counter(page) " of " counter(pages); }
    </style>
</head>
<body>

<div class="container">

    <!-- HEADER -->
    <div class="header" style="position:relative; padding-top:6px;">
        <!-- Control Number - Top Left -->
        <div style="position:absolute; left:0; top:0; width:120px;">
            <div style="border:1px solid #000; padding:4px; font-size:8px; text-align:center; background:#fff;">
                <div style="font-weight:700; margin-bottom:2px;">Control No.</div>
                <div style="border-bottom:1px solid #000; height:16px;"></div>
            </div>
        </div>

        <!-- Centered Content: Logo + University Info -->
        <div style="display:flex; justify-content:center; align-items:flex-start; gap:15px;">
            <!-- Logo -->
            <div style="flex-shrink:0;">
                @if(!empty($logos['logo']))
                    <img class="logo" src="{{ $logos['logo'] }}" alt="logo">
                @endif
            </div>

            <!-- University Info -->
            <div style="text-align:center;">
                <div class="title">University of Science and Technology of Southern Philippines</div>
                <div class="subtitle" style="margin-top:4px;">
                    Alubijid | Balubal | Cagayan de Oro | Claveria | Jasaan | Oroquieta | Panaon | Villanueva
                </div>
                <div class="subtitle" style="font-weight:700; margin-top:6px;">GUIDANCE AND COUNSELING SERVICES</div>
            </div>
        </div>

        <!-- Document Code - Top Right -->
        <div style="position:absolute; right:0; top:0; width:100px;">
            <div style="border:1px solid #000; padding:1px; font-size:5px; text-align:center; background:#fff;">
                <div style="background:#1b2a6b; color:#fff; font-weight:700; padding:1px;">Document Code No.</div>
                <div style="font-weight:700; font-size:6px; padding:2px 0;">{{ $documentCode->document_code_no ?? 'FM-USTP-GCS-01' }}</div>
                <div style="display:flex; font-size:6px; border-top:1px solid #000;">
                    <div style="flex:1; border-right:1px solid #000; padding:1px;">Rev. No.</div>
                    <div style="flex:1; padding:1px;">Effective Date</div>
                </div>
                <div style="display:flex; font-size:6px; border-top:1px solid #000;">
                    <div style="flex:1; border-right:1px solid #000; padding:1px;">{{ $documentCode->revision_no ?? '00' }}</div>
                    <div style="flex:1; padding:1px;">{{ $documentCode->effective_date ?? '07.01.23' }}</div>
                </div>
                <div style="border-top:1px solid #000; padding:2px; font-size:6px; font-weight:700;"><span class="page-number"></span></div>
            </div>
        </div>
    </div>

    <div style="text-align:center; margin-top:10px; font-weight:700;">HELP US SERVE YOU BETTER!</div>

    <!-- INTRO -->
    <div class="section">
        This Client Satisfaction Measurement (CSM) tracks the customer experience of government offices.
        Your feedback will help improve public service.
    </div>

    <!-- CLIENT INFO -->
    <div class="section">
        <div class="row">
            <div class="field">Client Type: <span class="line"></span></div>
            <div class="field">Date: <span class="line"></span></div>
        </div>
        <div class="row">
            <div class="field">Sex: ☐ Male ☐ Female</div>
            <div class="field">Age: <span class="line"></span></div>
        </div>
        <div class="row">
            <div class="field">Region of residence: <span class="line"></span></div>
            <div class="field">Service Availed: <span class="line"></span></div>
        </div>
    </div>

    <!-- CC INSTRUCTION -->
    <div class="instruction">
        INSTRUCTIONS: Check mark (✔) your answer to the Citizen's Charter (CC) questions.
    </div>

    <!-- CC QUESTIONS -->
    <div class="section checkbox-group">
        <strong>CC1. Awareness of CC</strong>
        <label>☐ I know what a CC is and I saw this office's CC.</label>
        <label>☐ I know what a CC is but I did NOT see this office's CC.</label>
        <label>☐ I learned of the CC only when I saw this office's CC.</label>
        <label>☐ I do not know what a CC is.</label>
    </div>

    <div class="section checkbox-group">
        <strong>CC2. If aware of CC, was it…?</strong>
        <label>☐ Easy to see</label>
        <label>☐ Somewhat easy to see</label>
        <label>☐ Difficult to see</label>
        <label>☐ Not visible at all</label>
        <label>☐ N/A</label>
    </div>

    <div class="section checkbox-group">
        <strong>CC3. How much did CC help you?</strong>
        <label>☐ Helped very much</label>
        <label>☐ Somewhat helped</label>
        <label>☐ Did not help</label>
        <label>☐ N/A</label>
    </div>

    <!-- TABLE INSTRUCTION -->
    <div class="instruction">
        INSTRUCTIONS: For OOQ 0–8, put a check mark (✔) on the column that best corresponds to your answer.
    </div>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th>Service Quality Dimensions</th>
                <th>Strongly Disagree</th>
                <th>Disagree</th>
                <th>Neither</th>
                <th>Agree</th>
                <th>Strongly Agree</th>
                <th>N/A</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>OOQ1. I am satisfied with the service that I availed.</td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td>OOQ2. I spent a reasonable amount of time for my transaction.</td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td>OOQ3. The office followed the transaction's requirements.</td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td>OOQ4. The steps needed were easy and simple.</td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td>OOQ5. I easily found information about my transaction.</td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td>OOQ6. I paid a reasonable amount of fees.</td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td>OOQ7. I was treated courteously by the staff.</td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td>OOQ8. The request result was clearly explained.</td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <p>Suggestions on how we can further improve our services (optional):</p>
        <div class="line" style="height: 30px;"></div>
        <div class="line" style="height: 30px;"></div>

        <p>Email address (optional): <span class="line"></span></p>
    </div>

    <div class="thank-you">THANK YOU!</div>

</div>

<div class="footer">
    <p style="font-size:10px; color:#666;">Confidential - For authorized personnel only <span class="page-number"></span></p>
</div>

</body>
</html>
