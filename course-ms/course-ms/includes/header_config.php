<?php
// Determine relative path to public assets based on where this file is included from
// If connection.php is alongside the current script, we are already at project root.
$path_prefix = file_exists('connection.php') ? '' : '../';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] },
                colors: {
                    honey: {
                        50: '#FFF8E1',
                        100: '#FFECB3',
                        400: '#FFCA28',
                        500: '#FFB300',
                        600: '#FFA000',
                        700: '#B45309'
                    }
                }
            }
        }
    };
</script>
<link rel="stylesheet" href="<?php echo $path_prefix; ?>dashboard_style.css">
<link rel="stylesheet" href="<?php echo $path_prefix; ?>style.css">
