<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_GET['ajax'])) {
    $area = floatval($_POST['area'] ?? $_GET['area'] ?? 0);
    $volumeSolids = floatval($_POST['volumeSolids'] ?? $_GET['volumeSolids'] ?? 0);
    $dft = floatval($_POST['dft'] ?? $_GET['dft'] ?? 0);
    
    // Calculate paint quantity
    $paintQuantity = ($area * $dft) / ($volumeSolids * 10);
    
    if(isset($_GET['ajax'])) {
        echo json_encode([
            'paintQuantity' => number_format($paintQuantity, 2),
            'wft' => number_format(($dft * 100) / $volumeSolids, 2)
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Paint Calculator</title>
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #2196F3;
            --accent-color: #FF9800;
            --background-color: #f5f5f5;
            --card-background: #ffffff;
            --text-color: #333333;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            color: var(--text-color);
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--card-background);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .slider-container {
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid var(--secondary-color);
        }

        .slider-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-weight: bold;
            color: var(--text-color);
        }

        .value-display {
            background: var(--secondary-color);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            min-width: 60px;
            text-align: center;
        }

        .slider {
            -webkit-appearance: none;
            width: 100%;
            height: 10px;
            border-radius: 5px;
            background: #ddd;
            outline: none;
            transition: 0.2s;
        }

        .slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--secondary-color);
            cursor: pointer;
            transition: 0.2s;
        }

        .slider::-webkit-slider-thumb:hover {
            background: var(--primary-color);
            transform: scale(1.2);
        }

        .result-container {
            background: linear-gradient(145deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-top: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .result-value {
            font-size: 2em;
            font-weight: bold;
            margin: 10px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .formula-box {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 5px solid var(--accent-color);
        }

        .formula-title {
            color: var(--accent-color);
            font-weight: bold;
            margin-bottom: 10px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Paint Quantity Calculator</h1>
        
        <div class="slider-container">
            <div class="slider-label">
                <span>Surface Area (m²)</span>
                <span class="value-display" id="areaValue">100</span>
            </div>
            <input type="range" min="1" max="1000" value="100" class="slider" id="areaSlider">
        </div>

        <div class="slider-container">
            <div class="slider-label">
                <span>Volume Solids (%)</span>
                <span class="value-display" id="volumeSolidsValue">60</span>
            </div>
            <input type="range" min="1" max="100" value="60" class="slider" id="volumeSolidsSlider">
        </div>

        <div class="slider-container">
            <div class="slider-label">
                <span>Dry Film Thickness (µm)</span>
                <span class="value-display" id="dftValue">50</span>
            </div>
            <input type="range" min="1" max="200" value="50" class="slider" id="dftSlider">
        </div>

        <div class="result-container">
            <h2>Required Paint Quantity</h2>
            <div class="result-value"><span id="paintQuantity">8.33</span> Liters</div>
            <div>Wet Film Thickness: <span id="wft">83.33</span> µm</div>
        </div>

        <div class="formula-box">
            <div class="formula-title">Formula Used:</div>
            <div>Paint Quantity (L) = (Area × DFT) ÷ (Volume Solids × 10)</div>
            <div>WFT (µm) = (DFT × 100) ÷ Volume Solids</div>
        </div>
    </div>

    <script>
        function updateCalculation() {
            const area = document.getElementById('areaSlider').value;
            const volumeSolids = document.getElementById('volumeSolidsSlider').value;
            const dft = document.getElementById('dftSlider').value;

            // Update displayed values
            document.getElementById('areaValue').textContent = area;
            document.getElementById('volumeSolidsValue').textContent = volumeSolids;
            document.getElementById('dftValue').textContent = dft;

            // Calculate paint quantity
            const paintQuantity = (area * dft) / (volumeSolids * 10);
            const wft = (dft * 100) / volumeSolids;

            // Update result with animation
            const quantityElement = document.getElementById('paintQuantity');
            const wftElement = document.getElementById('wft');
            
            quantityElement.textContent = paintQuantity.toFixed(2);
            wftElement.textContent = wft.toFixed(2);
            
            // Add animation class
            quantityElement.classList.remove('animate');
            wftElement.classList.remove('animate');
            void quantityElement.offsetWidth; // Trigger reflow
            quantityElement.classList.add('animate');
            wftElement.classList.add('animate');
        }

        // Add event listeners to sliders
        document.getElementById('areaSlider').addEventListener('input', updateCalculation);
        document.getElementById('volumeSolidsSlider').addEventListener('input', updateCalculation);
        document.getElementById('dftSlider').addEventListener('input', updateCalculation);

        // Initial calculation
        updateCalculation();
    </script>
</body>
</html>