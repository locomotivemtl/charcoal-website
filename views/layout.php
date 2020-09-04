<?php

$url = 'https://charcoal.locomotive.ca/'.@$langCode;

?><!DOCTYPE html>
<html class="has-no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#4D84F1">

        <title>Charcoal — <?php echo $tagline; ?></title>

        <link rel="canonical" href="<?php echo $url; ?>" hreflang="<?php echo $langCode; ?>">

        <link rel="alternate" href="https://charcoal.locomotive.ca/fr" hreflang="fr" title="Français">
        <link rel="alternate" href="https://charcoal.locomotive.ca/en" hreflang="en" title="English">

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,400italic,500,700,300">
        <link rel="stylesheet" href="assets/main.css">
    </head>
    <body data-section="work" data-template="generic" data-ident="charcoal" class="is-loading is-initial-load is-repeat-visitor u-background u-foreground t-work">

        <div class="c-loader u-foreground-accent-reverse">
            <div class="c-loader_figure"></div>
        </div>

        <div class="o-main-wrap u-background">

            <main class="o-main js-main u-background" id="main">

                <style>
                    .u-background                { background-color: #4D84F1; }
                    .u-foreground                { color:            #FFFFFF; } /* #C8C9B9 */
                    .u-background-reverse        { background-color: #FFFFFF; } /* #C8C9B9 */
                    .u-foreground-reverse        { color:            #4D84F1; }
                    .u-background-accent         { background-color: #FFFFFF; } /* #C8C9B9 */
                    .u-foreground-accent         { color:            #4D84F1; }
                    .u-background-accent-reverse { background-color: #4D84F1; }
                    .u-foreground-accent-reverse { color:            #FFFFFF; } /* #C8C9B9 */
                </style>

                <?php echo $content; ?>

            </main>

        </div>

        <script src="assets/jquery.js"></script>
        <script src="assets/vendors.js"></script>
        <script src="assets/app.js"></script>
	</body>
</html>
