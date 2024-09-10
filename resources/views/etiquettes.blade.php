<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Étiquettes</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 10mm;
            padding-top: 20mm;
            font-family: Arial, sans-serif;
            width: 210mm;
            height: 297mm;
        }
        .page {

            page-break-after: always;
        }
        .page:last-child {
            page-break-after: avoid;
        }
        .etiquette {
            float: left;
            width: 60mm;
            height: 35mm;
            border: 1px solid black;
            box-sizing: border-box;
            text-align: center;
            font-size: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 2px;
        }
        .barcode {
            display: flex;
            justify-content: center;
            width: 100%;
            max-width: 60mm; /* Limite maximale de la largeur du code-barres */
            overflow: hidden;
        }
        .barcode div {
            display: inline-block;
        }
        .clearfix {
            clear: both;
        }
    </style>
</head>
<body>
@php
    use Picqer\Barcode\BarcodeGeneratorHTML;

    $barcodeTypes = [
        'CODE128' => BarcodeGeneratorHTML::TYPE_CODE_128,
        'CODE39' => BarcodeGeneratorHTML::TYPE_CODE_39,
        'EAN13' => BarcodeGeneratorHTML::TYPE_EAN_13,
        'UPC' => BarcodeGeneratorHTML::TYPE_UPC_A,
        // Ajoutez d'autres types de code-barres ici si nécessaire
    ];

    $generator = new BarcodeGeneratorHTML();

    // Fonction pour tronquer le texte
    if (!function_exists('truncate')) {
        function truncate($text, $maxChars) {
            return strlen($text) > $maxChars ? substr($text, 0, $maxChars) . '...' : $text;
        }
    }
@endphp
@foreach ($products->chunk(21) as $chunkIndex => $chunk)
    <div class="page">
        @foreach ($chunk as $index => $product)
            @php
                $barcodeHtml = isset($product->Type_barcode) && isset($barcodeTypes[$product->Type_barcode])
                    ? $generator->getBarcode($product->code, $barcodeTypes[$product->Type_barcode])
                    : null;
            @endphp
            <div class="etiquette">
                @if($barcodeHtml)
                    <div class="barcode" >
                        {!! $barcodeHtml !!}
                    </div>
                    <strong style="font-size: 14px;padding-bottom: 5px">{{  $product->code, 24 }}</strong><br><br>
                    <span style="font-size: 12px">{{ truncate($product->name, 55) }}</span><br>
                    <span style="font-size: 12px">{{ truncate($product->category->name, 55) }}</span>
                @else
                    <strong style="font-size: 16px;padding-bottom: 5px">Type de code-barres</strong><br><br>
                    <span style="font-size: 16px">Not allowed</span><br>
                @endif
            </div>
            @if (($index + 1) % 3 == 0)
                <div class="clearfix"></div>
            @endif
        @endforeach
    </div>
@endforeach
</body>
</html>
