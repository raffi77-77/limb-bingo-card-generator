<html lang="en">
<head>
    <title><?php echo get_bloginfo(); ?></title>
    <style type="text/css">
        .lbcg-email-content {
            max-width: 350px;
            margin: 10px;
            padding: 10px;
            background-color: #e4dcf3;
            display: grid;
            border-radius: 8px;
        }

        .lbcg-email-content h3 {
            margin: 1rem 0;
            font-size: 1.3rem;
            font-family: arvo, SFMono-Regular, Menlo, Monaco, Consolas, liberation mono, courier new, monospace;
            font-weight: 500;
            line-height: 1.35;
            color: #fc155c;
        }

        .lbcg-email-content p {
            margin: 0 0 1rem 0;
            font-family: arvo, SFMono-Regular, Menlo, Monaco, Consolas, liberation mono, courier new, monospace;
        }

        .lbcg-email-content a {
            text-decoration: none;
            color: #212529;
            background-color: #ffc107;
            border-color: #ffc107;
            line-height: 2.5;
            border-radius: .3rem;
            text-align: center !important;
            font-family: arvo, SFMono-Regular, Menlo, Monaco, Consolas, liberation mono, courier new, monospace;
            width: 100%;
            max-width: 175px;
            font-size: 95%;
            float: left;
        }

        .lbcg-email-content a:hover, a:focus {
            color: #212529;
            background-color: #e0a800;
            border-color: #d39e00;
        }

        .lbcg-email-content a:focus {
            box-shadow: 0 0 0 0.2rem rgba(222, 170, 12, .5);
        }
    </style>
</head>
<body>
<div class="lbcg-email-content">
    <h3><?php echo get_bloginfo(); ?></h3>
    <p>Visit to your bingo card page</p>
    <a href="<?php echo $bc_link; ?>" target="_blank">View card</a>
</div>
</body>
</html>
