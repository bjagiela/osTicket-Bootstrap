<?php
/*********************************************************************
    index.php

    Helpdesk landing page. Please customize it to fit your needs.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('client.inc.php');

require_once INCLUDE_DIR . 'class.page.php';

$section = 'home';

// Landing page type choice
if ($thisclient && is_object($thisclient) && $thisclient->isValid() && !$thisclient->isGuest()) {
    $landingBody = false;
    $clientLoggedIn = true;
    require(CLIENTINC_DIR.'header.inc.php');
    require(CLIENTINC_DIR.'landing-user.inc.php');
?>

<!-- Knowledge base preview -->
<div class="container">
    <?php
    if($cfg && $cfg->isKnowledgebaseEnabled()){
        //FIXME: provide ability to feature or select random FAQs ??
    ?>
    <?php
    $cats = Category::getFeatured();
    if ($cats->all()) { ?>
    <hr class="mt-5">
    <div class="row">
        <div class="col-12">
            <h4 class="text-center"><i class="bi bi-journal-bookmark"></i> <?php echo __('Featured Knowledge Base Articles'); ?></h4>
        </div>
    </div>

    <?php
        if ($cfg && $cfg->isKnowledgebaseEnabled()) { ?>
        <div class="row gy-4 gx-3">
            <div class="ms-auto me-auto col-12 col-md-6 ">
                <form method="get" action="kb/faq.php">
                    <input type="hidden" name="a" value="search"/>
                    <div class="input-group mb-3 mt-1">
                        <input type="text" name="q" class="form-control shadow-sm" placeholder="<?php echo __('Search our knowledge base'); ?>" aria-label="Recipient's username" aria-describedby="button-addon2">
                        <button class="btn btn-outline-secondary shadow-sm" type="submit"><i class="bi bi-search"></i> <?php echo __('Search'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    <?php } ?>

    <div class="row gy-4 gx-3 justify-content-center">
    <?php
    }
    foreach ($cats as $C) { ?>
        <?php foreach ($C->getTopArticles() as $F) { ?>
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <!-- <img src="..." class="card-img-top" alt="..."> -->
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $F->getQuestion(); ?></h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary"><?php echo $C->getName(); ?></h6>
                        <p class="card-text mb-0"><?php echo $F->getTeaser(); ?></p>
                        <a href="<?php echo ROOT_PATH; ?>kb/faq.php?id=<?php echo $F->getId(); ?>" class="text-dark">Zobacz więcej</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php
        }
    }
    ?>
    </div>
</div>

    <?php require(CLIENTINC_DIR.'footer.inc.php'); ?>

<?php
} else {
    $landingBody = true;
    $clientLoggedIn = false;
    require(CLIENTINC_DIR.'header.inc.php');
    require(CLIENTINC_DIR.'landing-guest.inc.php');
}
?>

    