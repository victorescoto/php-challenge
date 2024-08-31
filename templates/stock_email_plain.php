Stock Data Received Successfully

Stock data for <?= $stockCode ?> was received successfully. Here are the details:

<?php foreach ($stockData as $key => $value): ?>
- <?= $key ?>: <?= $value . PHP_EOL ?>
<?php endforeach; ?>
