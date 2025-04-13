
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Sensor Data Submission</title>
</head>
<body>
    <h2>Test Sensor Data Submission</h2>
    <form action="esp32data.php" method="POST">
        <label for="api_key">API Key:</label>
        <input type="text" id="api_key" name="api_key" value="tPmAT5Ab3j7F9" required><br><br>

        <label for="temperature">Temperature (°C):</label>
        <input type="number" step="0.01" id="temperature" name="temperature" required><br><br>

        <label for="turbidity">Turbidity:</label>
        <input type="text" class="form-control" id="turbidity" name="turbidity" required><br><br>

        <label for="phlevel">pH Level:</label>
        <input type="number" step="0.01" id="phlevel" name="phlevel" required><br><br>

        <label for="nh3">NH3 Concentration:</label>
        <input type="number" step="0.01" id="nh3" name="nh3" required><br><br>

        <!-- Pass the device_number as a hidden input -->
        <input type="hidden" name="device_number" value="DEV0001">

        <button type="submit">Submit</button>
    </form>
</body>
</html>
