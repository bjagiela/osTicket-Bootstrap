<?php
$title=($cfg && is_object($cfg) && $cfg->getTitle())
    ? $cfg->getTitle() : 'osTicket :: '.__('Support Ticket System');
$signin_url = ROOT_PATH . "login.php"
    . ($thisclient ? "?e=".urlencode($thisclient->getEmail()) : "");
$signout_url = ROOT_PATH . "logout.php?auth=".$ost->getLinkToken();

header("Content-Type: text/html; charset=UTF-8");
// header("Content-Security-Policy: frame-ancestors ".$cfg->getAllowIframes()."; script-src 'self' 'unsafe-inline'; object-src 'none'");

if (($lang = Internationalization::getCurrentLanguage())) {
    $langs = array_unique(array($lang, $cfg->getPrimaryLanguage()));
    $langs = Internationalization::rfc1766($langs);
    header("Content-Language: ".implode(', ', $langs));
}
?>
<!DOCTYPE html>
<html style="height: 100%;" <?php
if ($lang
        && ($info = Internationalization::getLanguageInfo($lang))
        && (@$info['direction'] == 'rtl'))
    echo ' dir="rtl" class="rtl"';
if ($lang) {
    echo ' lang="' . $lang . '"';
}

// Dropped IE Support Warning
if (osTicket::is_ie())
    $ost->setWarning(__('osTicket no longer supports Internet Explorer.'));
?>>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo Format::htmlchars($title); ?></title>
    <meta name="description" content="customer support platform">
    <meta name="keywords" content="osTicket, Customer support system, support ticket system">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/osticket.css" media="screen">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/print.css" media="print">
    <link type="text/css" href="<?php echo ROOT_PATH; ?>css/ui-lightness/jquery-ui-1.13.2.custom.min.css"
        rel="stylesheet" media="screen" />
    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>css/jquery-ui-timepicker-addon.css" media="all">
    <!-- <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/thread.css" media="screen">
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/redactor.css" media="screen"> -->
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/flags.css">
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/rtl.css"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/select2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="<?php echo ROOT_PATH ?>images/oscar-favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="<?php echo ROOT_PATH ?>images/oscar-favicon-16x16.png" sizes="16x16" />
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-3.7.0.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-1.13.2.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-timepicker-addon.js"></script>
    <script src="<?php echo ROOT_PATH; ?>js/osticket.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/filedrop.field.js"></script>
    <script src="<?php echo ROOT_PATH; ?>js/bootstrap-typeahead.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-plugins.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-osticket.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/select2.min.js"></script>
    <?php
    if($ost && ($headers=$ost->getExtraHeaders())) {
        echo "\n\t".implode("\n\t", $headers)."\n";
    }

    // Offer alternate links for search engines
    // @see https://support.google.com/webmasters/answer/189077?hl=en
    if (($all_langs = Internationalization::getConfiguredSystemLanguages())
        && (count($all_langs) > 1)
    ) {
        $langs = Internationalization::rfc1766(array_keys($all_langs));
        $qs = array();
        parse_str($_SERVER['QUERY_STRING'], $qs);
        foreach ($langs as $L) {
            $qs['lang'] = $L; ?>
        <link rel="alternate" href="//<?php echo $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']); ?>?<?php
            echo http_build_query($qs); ?>" hreflang="<?php echo $L; ?>" />
<?php
        } ?>
        <link rel="alternate" href="//<?php echo $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']); ?>"
            hreflang="x-default" />
<?php
    }
    ?>
</head>

<?php 
    if ($landingBody && !$clientLoggedIn) {
        echo '<body class="bg-landing">';
    }
    else {
        echo '<body>';
    }
?>
        <?php
        if($ost->getError())
            echo sprintf('<div class="alert alert-danger">%s</div>', $ost->getError());
        elseif($ost->getWarning())
            echo sprintf('<div class="alert alert-warning">%s</div>', $ost->getWarning());
        elseif($ost->getNotice())
            echo sprintf('<div class="alert alert-info">%s</div>', $ost->getNotice());
        ?>

        <nav class="navbar bg-dark border-bottom border-body navbar-expand-lg" data-bs-theme="dark">
            <div class="container-fluid">
                <!-- <a class="navbar-brand" href="#">Navbar</a> -->
                <a class="navbar-brand" href="<?php echo ROOT_PATH; ?>index.php">
                    <img style="width: 30px; height: 24px; object-fit: fill;" src="<?php echo ROOT_PATH; ?>logo.php" alt="<?php echo $ost->getConfig()->getTitle(); ?>"/>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <ul class="navbar-nav mb-lg-0 me-auto">
                    <?php
                        if($nav){ ?>
                            <?php
                            if($nav && ($navs=$nav->getNavLinks()) && is_array($navs)){
                                foreach($navs as $name =>$nav) {
                                    $iconhtml = "";
                                    switch ($nav['href']) {
                                        case "index.php":
                                            $iconhtml = '<i class="bi bi-house"></i>';
                                            break;
                                        case "kb/index.php":
                                            $iconhtml = '<i class="bi bi-journal-bookmark"></i>';
                                            break;
                                        case "open.php":
                                            $iconhtml = '<i class="bi bi-ticket-perforated"></i>';
                                            break;
                                        case "tickets.php" || "view.php":
                                            $iconhtml = '<i class="bi bi-card-checklist"></i>';
                                            break;
                                        default:
                                            $iconhtml = "";
                                    }
                                    echo sprintf('<li class="nav-item"><a class="nav-link %s" href="%s">%s %s</a></li>',$nav['active']?'active':'',(ROOT_PATH.$nav['href']), $iconhtml, $nav['desc']);
                                }
                            } ?>
                        <?php
                        }else{ ?>
                        <hr>
                    <?php } ?>
                </ul>

                <ul class="navbar-nav mb-lg-0 ms-auto">
                    <?php
                        if ($thisclient && is_object($thisclient) && $thisclient->isValid()
                            && !$thisclient->isGuest()) {
                    ?>
                        <li class="nav-item dropdown">
                            <a class="dropdown-toggle btn btn-outline-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo Format::htmlchars($thisclient->getName()); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo ROOT_PATH; ?>profile.php"><?php echo __('Profile'); ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo ROOT_PATH; ?>tickets.php"><?php echo sprintf(__('Tickets <b>(%d)</b>'), $thisclient->getNumTickets()); ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a>
                                </li>
                            </ul>
                        </li>
                    <?php
                    } elseif($nav) {
                        if ($cfg->getClientRegistrationMode() == 'public') { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="#"><?php echo __('Guest User'); ?></a>
                            </li>
                        <?php
                        }
                        if ($thisclient && $thisclient->isValid() && $thisclient->isGuest()) { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a>
                            </li>
                        <?php
                        }
                        elseif ($cfg->getClientRegistrationMode() != 'disabled') { ?>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="<?php echo $signin_url; ?>"><i class="bi bi-door-open"></i> <?php echo __('Sign In'); ?></a>
                        </li>
                        <?php
                        }
                    } ?>
                </ul>
                </div>
            </div>
        </nav>

        <div>
            <div class="pull-right flush-right">
            <p>
             
            </p>
            <p>
                <?php
                if (($all_langs = Internationalization::getConfiguredSystemLanguages())
                    && (count($all_langs) > 1)
                ) {
                    $qs = array();
                    parse_str($_SERVER['QUERY_STRING'], $qs);
                    foreach ($all_langs as $code=>$info) {
                        list($lang, $locale) = explode('_', $code);
                        $qs['lang'] = $code;
                ?>
                        <a class="flag flag-<?php echo strtolower($info['flag'] ?: $locale ?: $lang); ?>"
                            href="?<?php echo http_build_query($qs);
                            ?>" title="<?php echo Internationalization::getLanguageDescription($code); ?>">&nbsp;</a>
                <?php }
                } ?>
            </p>
            </div>
        </div>

        <div class="container">
            <?php if($errors['err']) { ?>
                <div class="alert alert-danger"><?php echo $errors['err']; ?></div>
            <?php }elseif($msg) { ?>
                <div class="alert alert-info"><?php echo $msg; ?></div>
            <?php }elseif($warn) { ?>
                <div class="alert alert-warning"><?php echo $warn; ?></div>
            <?php } ?>
        </div>
