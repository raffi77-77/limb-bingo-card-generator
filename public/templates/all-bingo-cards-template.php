<?php
/**
 * The Template for displaying all generated bingo cards
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

global $post;
$all = BingoCardHelper::generate_all_content_info($post->ID, 500, (int)$_GET['bcc']);

?>

<style type="text/css" media="print">
    @page {
        size: <?php echo !empty($_GET['bcs']) && $_GET['bcs'] == 2 ? 'A4 landscape' : 'A4 portrait'; ?>;
    }
</style>

<?php include __DIR__ . '/props-template.php'; ?>

<div class="custom-container">
    <main class="lbcg-parent">
        <div class="lbcg-print-wrap">
            <div class="lbcg-print-wrap-in lbcg-print-wrap-in-<?=$_GET['bcs'] ?>"><!-- 1 ej -->
                <div class="lbcg-print-wrap-card-holder">
                    <div class="lbcg-card-wrap">
                        <div class="lbcg-card">
                            <div class="lbcg-card-header-holder">
                                <div class="lbcg-card-header">
                                    <span class="lbcg-card-header-text">B I N G O</span>
                                </div>
                                <div class="lbcg-card-subtitle">
                                    <span class="lbcg-card-subtitle-text"><span>B</span><span>I</span><span>N</span><span>G</span><span>O</span></span>
                                </div>
                            </div>
                            <div class="lbcg-card-body">
                                <div class="lbcg-card-body-grid lbcg-grid-5">
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text">★</span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                    <div class="lbcg-card-col">
                                        <span class="lbcg-card-text"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lbcg-print-wrap-in"><!-- 1 ej -->
                test 2
            </div>
        </div>
    </main>
</div>

<?php
get_footer();
