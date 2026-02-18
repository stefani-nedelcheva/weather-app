<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Service App</title>
</head>
<body style="font-family: sans-serif; max-width: 640px; margin: 40px auto;">
<h1>Weather Service App</h1>
<form id="form">
    <input id="city" type="text" placeholder="Enter city (e.g. Sofia)" required />
    <button type="submit">Get Weather</button>
</form>

<pre id="result" style="margin-top: 20px;"></pre>

<script>
    const form = document.getElementById('form');
    const city = document.getElementById('city');
    const result = document.getElementById('result');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const response = await fetch(`/api/weather?city=${encodeURIComponent(city.value)}`);
        result.textContent = JSON.stringify(await response.json(), null, 2);
    });
</script>
</body>
</html>
