<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" media="screen" href="{{ secure_asset('css/header.css') }}" />
</head>
<body> 
    <header>
        <div class="seers-cms-header-logo">
            <img src="{{ asset('images/logo-seersai.svg') }}" alt="Logo">
        </div>
        <div class="seers-cms-header-nav">
        
            <p class="seers-cms-header-dynamic-website"><?php echo $sitename; ?></p>
        
            <button class="seers-cms-header-button seers-paid-feature-opener customizeSeersBtn" data-tab="Preferences" name="headerpremium">Get More Features</button>

            <button class="seers-cms-toggle-button">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>
</body>
</html>
