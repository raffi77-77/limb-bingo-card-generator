<html lang="en">
<head>
    <title><?php echo get_bloginfo(); ?></title>
    <style type="text/css">
        .lbcg-email-content {
            max-width: 350px;
            margin: 10px;
            padding: 10px;
            font-size: 1rem;
            font-family: arvo, SFMono-Regular, Menlo, Monaco, Consolas, liberation mono, courier new, monospace;
            background-color: #e4dcf3;
            display: grid;
            border-radius: 8px;
        }

        .lbcg-email-content h3 {
            margin: 1rem 0;
            font-size: 1.3rem;
            font-weight: 500;
            line-height: 1.35;
            color: #fc155c;
        }

        .lbcg-email-content p {
            margin: 0 0 1rem 0;
        }

        .lbcg-email-content p.lbcg-note {
            margin-top: 1rem;
            font-size: .8rem;
        }

        .lbcg-email-content a.lbcg-button {
            text-decoration: none;
            color: #212529;
            background-color: #ffc107;
            border-color: #ffc107;
            line-height: 2.5;
            border-radius: .3rem;
            text-align: center !important;
            width: 100%;
            max-width: 175px;
            float: left;
        }

        .lbcg-email-content a.lbcg-button:hover, a.lbcg-button:focus {
            color: #212529;
            background-color: #e0a800;
            border-color: #d39e00;
        }

        .lbcg-email-content a.lbcg-button:focus {
            box-shadow: 0 0 0 0.2rem rgba(222, 170, 12, .5);
        }

        .lbcg-email-content a.lbcg-email {
            color: #6f42c1;
            text-decoration: none;
            background-color: transparent;
        }

        .lbcg-email-content a.lbcg-email:hover, a.lbcg-email:hover {
            color: #59359a;
        }
    </style>
</head>
<body>
<div class="lbcg-email-content">
    <h3><?php echo get_bloginfo(); ?></h3>
	<?php echo ! empty( $author_message ) ? '<div>' . $author_message . '</div>' : ''; ?>
    <p>Visit to your bingo card page</p>
    <a href="<?php echo $bc_link; ?>" class="lbcg-button" target="_blank">View card</a>
    <p class="lbcg-note">This card was sent to you by <a href="mailto:<?php echo $author_email; ?>" class="lbcg-email"><?php echo $author_email; ?></a>, we do not store your email address.</p>
</div>
</body>
</html>
