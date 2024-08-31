<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { padding: 20px; }
        h2 { color: #333; }
        ul { list-style-type: none; padding: 0; }
        li { margin-bottom: 5px; }
        strong { color: #555; }
    </style>
</head>
<body>
    <div class='container'>
        <h2>Stock Data Received Successfully</h2>
        <p>Stock data for <strong><?= $stockCode ?></strong> was received successfully. Here are the details:</p>
        <ul>
            <?php foreach ($stockData as $key => $value): ?>
                <li><strong><?= $key ?></strong>: <?= $value ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
